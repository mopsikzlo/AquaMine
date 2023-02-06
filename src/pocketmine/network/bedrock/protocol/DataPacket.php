<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>

use InvalidArgumentException;
use pocketmine\block\BlockIds;
use pocketmine\item\Durable;
use pocketmine\item\Item;
use pocketmine\math\Vector2;
use pocketmine\nbt\LittleEndianNbtSerializer;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\LongTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\nbt\TreeRoot;
use pocketmine\network\bedrock\palette\BlockPalette;
use pocketmine\network\bedrock\palette\ItemPalette;
use pocketmine\network\bedrock\protocol\types\actor\ActorLink;
use pocketmine\network\bedrock\protocol\types\actor\ActorMetadataTypes;
use pocketmine\network\bedrock\protocol\types\CommandOriginData;
use pocketmine\network\bedrock\protocol\types\Experiments;
use pocketmine\network\bedrock\protocol\types\inventory\ItemInstance;
use pocketmine\network\bedrock\protocol\types\inventory\LegacySetItemSlot;
use pocketmine\network\bedrock\protocol\types\itemStack\StackRequestSlotInfo;
use pocketmine\network\bedrock\protocol\types\skin\PersonaPiece;
use pocketmine\network\bedrock\protocol\types\skin\PieceTintColor;
use pocketmine\network\bedrock\protocol\types\skin\SerializedSkinImage;
use pocketmine\network\bedrock\protocol\types\skin\Skin;
use pocketmine\network\bedrock\protocol\types\skin\SkinAnimation;
use pocketmine\network\bedrock\protocol\types\StructureSettings;
use pocketmine\network\mcpe\NetworkBinaryStream;
use pocketmine\network\mcpe\NetworkNbtSerializer;
use pocketmine\network\mcpe\protocol\DataPacket as MCPEDataPacket;
use pocketmine\utils\BinaryDataException;
use UnexpectedValueException;
use function count;
use function get_class;
use function gettype;
use function is_object;

abstract class DataPacket extends MCPEDataPacket{

	protected const DAMAGE_TAG = "Damage"; //TAG_Int
	protected const DAMAGE_TAG_CONFLICT_RESOLUTION = "___Damage_ProtocolCollisionResolution___";
	protected const PM_META_TAG = "___Meta___";

	public function decode(){
		$this->rewind();
		$this->getUnsignedVarInt();
		$this->decodePayload();
		$this->wasDecoded = true;
	}

	public function encode(){
		$this->reset();
		$this->putUnsignedVarInt(static::NETWORK_ID);
		$this->encodePayload();
		$this->isEncoded = true;
	}

	public function reset() : void{
		$this->buffer = "";
		$this->offset = 0;
	}

	public function getItemStack(\Closure $readExtraCrapInTheMiddle) : Item{
		$netId = $this->getVarInt();
		if($netId === 0){
			return Item::air();
		}

		$cnt = $this->getLShort();
		$netData = $this->getUnsignedVarInt();

		[$id, $meta] = ItemPalette::getLegacyFromRuntimeId($netId, $netData);

		$readExtraCrapInTheMiddle($this);

		$this->getVarInt();

		$extraData = new NetworkBinaryStream($this->getString());
		return (static function() use ($extraData, $netId, $id, $meta, $cnt) : Item {
			$nbtLen = $extraData->getLShort();

			/** @var CompoundTag|null $nbt */
			$nbt = null;
			if($nbtLen === 0xffff){
				$c = $extraData->getByte();
				if($c !== 1){
					throw new UnexpectedValueException("Unexpected NBT data version $c");
				}

				$nbt = (new LittleEndianNbtSerializer())->read($extraData->buffer, $extraData->offset, 512)->mustGetCompoundTag();

				if($nbt->hasTag(self::DAMAGE_TAG, IntTag::class)){ //a hack: 1.12+ meta format
					$meta = $nbt->getInt(self::DAMAGE_TAG);
					$nbt->removeTag(self::DAMAGE_TAG);
				}elseif(($metaTag = $nbt->getTag(self::PM_META_TAG)) instanceof IntTag){
					//TODO HACK: This foul-smelling code ensures that we can correctly deserialize an item when the
					//client sends it back to us, because as of 1.16.220, blockitems quietly discard their metadata
					//client-side. Aside from being very annoying, this also breaks various server-side behaviours.
					$meta = $metaTag->getValue();
					$nbt->removeTag(self::PM_META_TAG);
				}

				if($nbt->hasTag(self::DAMAGE_TAG_CONFLICT_RESOLUTION)){
					$nbt->setTag(self::DAMAGE_TAG, $nbt->getTag(self::DAMAGE_TAG_CONFLICT_RESOLUTION));
					$nbt->removeTag(self::DAMAGE_TAG_CONFLICT_RESOLUTION);
				}

				if($nbt->hasTag("map_uuid", LongTag::class)){ // 1.1 compatibility hack
					$nbt->setString("map_uuid", (string) $nbt->getLong("map_uuid"));
				}

				if($nbt->count() === 0){
					$nbt = null;
				}
			}elseif($nbtLen !== 0){
				throw new UnexpectedValueException("Unexpected fake NBT length $nbtLen");
			}


			//TODO
			$canPlaceOn = $extraData->getLInt();
			if($canPlaceOn > 128){
				throw new UnexpectedValueException("Too many canPlaceOn: $canPlaceOn");
			}elseif($canPlaceOn > 0){
				for($i = 0; $i < $canPlaceOn; ++$i){
					$extraData->get($extraData->getLShort());
				}
			}

			//TODO
			$canDestroy = $extraData->getLInt();
			if($canDestroy > 128){
				throw new UnexpectedValueException("Too many canDestroy: $canDestroy");
			}elseif($canDestroy > 0){
				for($i = 0; $i < $canDestroy; ++$i){
					$extraData->get($extraData->getLShort());
				}
			}

			if($netId === ItemPalette::getRuntimeFromStringId("minecraft:shield")){ //SHIELD
				$extraData->getLLong(); //"blocking tick" (ffs mojang)
			}

			if(!$extraData->feof()){
				throw new \UnexpectedValueException("Unexpected trailing extradata for network item $netId");
			}

			return Item::get($id, $meta, $cnt, $nbt);
		})();
	}

	public function putItemStack(Item $item, \Closure $writeExtraCrapInTheMiddle) : void{
		if($item->isNull()){
			$this->putVarInt(0);
			return;
		}

		$coreData = $item->getDamage();
		[$netId, $netData] = ItemPalette::getRuntimeFromLegacyId($item->getId(),$item instanceof Durable ? 0 : $item->getDamage());

		$this->putVarInt($netId);
		$this->putLShort($item->getCount());
		$this->putUnsignedVarInt($netData);

		$writeExtraCrapInTheMiddle($this);

		$blockRuntimeId = 0;
		$isBlockItem = $item->getId() < 256;
		if($isBlockItem){
			$block = $item->getBlock();
			if($block->getId() !== BlockIds::AIR){
				$blockRuntimeId = BlockPalette::getRuntimeFromLegacyId($block->getId(), $block->getDamage());
			}
		}
		$this->putVarInt($blockRuntimeId);

		$isDurable = $item instanceof Durable;
		$nbt = null;
		if($item->hasCompoundTag() or $isDurable){
			$nbt = clone $item->getNamedTag();
		}

		if($isDurable and $coreData !== 0){
			if($nbt->hasTag(self::DAMAGE_TAG)){
				$nbt->setTag(self::DAMAGE_TAG_CONFLICT_RESOLUTION, $nbt->getTag(self::DAMAGE_TAG));
			}

			if($nbt->hasTag("map_uuid", StringTag::class)){ // 1.1 compatibility
				$nbt->setLong("map_uuid", (int) $nbt->getString("map_uuid"));
			}

			$nbt->setInt(self::DAMAGE_TAG, $coreData);
		}elseif($isBlockItem && $coreData !== 0){
			//TODO HACK: This foul-smelling code ensures that we can correctly deserialize an item when the
			//client sends it back to us, because as of 1.16.220, blockitems quietly discard their metadata
			//client-side. Aside from being very annoying, this also breaks various server-side behaviours.
			if($nbt === null){
				$nbt = new CompoundTag();
			}
			$nbt->setInt(self::PM_META_TAG, $coreData);
		}

		$this->putString((static function () use($nbt, $netId): string {
			$extraData = new NetworkBinaryStream();
			if($nbt !== null){
				$extraData->putLShort(0xffff);
				$extraData->putByte(1); //TODO: NBT data version (?)
				$extraData->put((new LittleEndianNbtSerializer())->write(new TreeRoot($nbt)));
			}else{
				$extraData->putLShort(0);
			}

			$extraData->putLInt(0); //CanPlaceOn entry count (TODO)
			$extraData->putLInt(0); //CanDestroy entry count (TODO)

			if($netId === ItemPalette::getRuntimeFromStringId("minecraft:shield")){ //SHIELD
				$this->putLLong(0); //"blocking tick" (ffs mojang)
			}
			return $extraData->buffer;
		})());
	}

	public function getItemStackWithoutStackId() : Item{
		return $this->getItemStack(function() : void{
			//NOOP
		});
	}

	public function putItemStackWithoutStackId(Item $item) : void{
		$this->putItemStack($item, function() : void{
			//NOOP
		});
	}

	public function getItemInstance() : ItemInstance{
		$stackId = 0;
		$stack = $this->getItemStack(function(DataPacket $in) use (&$stackId) : void{
			$hasNetId = $in->getBool();
			if($hasNetId){
				$stackId = $in->getVarInt();
			}
		});

		return new ItemInstance($stackId, $stack);
	}

	/**
	 * @param ItemInstance|Item $itemStack
	 */
	public function putItemInstance($itemStack) : void{
		if($itemStack instanceof Item){
			$itemStack = ItemInstance::legacy($itemStack);
		}else if(!($itemStack instanceof ItemInstance)){
			throw new InvalidArgumentException("Expected \$itemStack to be ItemInstance or Item, got " . (is_object($itemStack) ? get_class($itemStack) : gettype($itemStack)));
		}
		$stackId = $itemStack->stackNetworkId;

		$this->putItemStack($itemStack->stack, function(DataPacket $out) use($stackId){
			$out->putBool($stackId !== 0);
			if($stackId !== 0){
				$out->putVarInt($stackId);
			}

			return $out->buffer;
		});
	}

	/**
	 * @return array, members are in the structure [name => [type, value, isPlayerModifiable]]
	 */
	public function getGameRules() : array{
		$count = $this->getUnsignedVarInt();
		$rules = [];
		for($i = 0; $i < $count; ++$i){
			$name = $this->getString();
			$isPlayerModifiable = $this->getBool();
			$type = $this->getUnsignedVarInt();
			$value = null;
			switch($type){
				case 1:
					$value = $this->getBool();
					break;
				case 2:
					$value = $this->getUnsignedVarInt();
					break;
				case 3:
					$value = $this->getLFloat();
					break;
			}

			$rules[$name] = [$type, $value, $isPlayerModifiable];
		}

		return $rules;
	}

	/**
	 * Writes a gamerule array, members should be in the structure [name => [type, value, isPlayerModifiable]]
	 */
	public function putGameRules(array $rules) : void{
		$this->putUnsignedVarInt(count($rules));
		foreach($rules as $name => $rule){
			$this->putString($name);
			$this->putBool($rule[2]);
			$this->putUnsignedVarInt($rule[0]);
			switch($rule[0]){
				case 1:
					$this->putBool($rule[1]);
					break;
				case 2:
					$this->putUnsignedVarInt($rule[1]);
					break;
				case 3:
					$this->putLFloat($rule[1]);
					break;
			}
		}
	}

	/**
	 * @return ActorLink
	 *
	 * @throws BinaryDataException
	 */
	protected function getActorLink() : ActorLink{
		$link = new ActorLink();

		$link->fromActorUniqueId = $this->getActorUniqueId();
		$link->toActorUniqueId = $this->getActorUniqueId();
		$link->type = $this->getByte();
		$link->immediate = $this->getBool();
		$link->riderInitiated = $this->getBool();

		return $link;
	}

	/**
	 * @param ActorLink $link
	 */
	protected function putActorLink(ActorLink $link) : void{
		$this->putActorUniqueId($link->fromActorUniqueId);
		$this->putActorUniqueId($link->toActorUniqueId);
		$this->putByte($link->type);
		$this->putBool($link->immediate);
		$this->putBool($link->riderInitiated);
	}

	/**
	 * Reads and returns an ActorUniqueID
	 * @return int
	 */
	public function getActorUniqueId() : int{
		return $this->getVarLong();
	}

	/**
	 * Writes an ActorUniqueID
	 *
	 * @param int $actorUniqueId
	 */
	public function putActorUniqueId(int $actorUniqueId) : void{
		$this->putVarLong($actorUniqueId);
	}

	/**
	 * Reads and returns an ActorRuntimeID
	 * @return int
	 */
	public function getActorRuntimeId() : int{
		return $this->getUnsignedVarLong();
	}

	/**
	 * Writes an ActorRuntimeID
	 *
	 * @param int $actorRuntimeId
	 */
	public function putActorRuntimeId(int $actorRuntimeId) : void{
		$this->putUnsignedVarLong($actorRuntimeId);
	}

	/**
	 * @return CommandOriginData
	 * @throws BinaryDataException
	 */
	protected function getCommandOriginData() : CommandOriginData{
		$result = new CommandOriginData();

		$result->type = $this->getUnsignedVarInt();
		$result->uuid = $this->getUUID();
		$result->requestId = $this->getString();

		if($result->type === CommandOriginData::ORIGIN_DEV_CONSOLE or $result->type === CommandOriginData::ORIGIN_TEST){
			$result->varlong1 = $this->getVarLong();
		}

		return $result;
	}

	protected function putCommandOriginData(CommandOriginData $data) : void{
		$this->putUnsignedVarInt($data->type);
		$this->putUUID($data->uuid);
		$this->putString($data->requestId);

		if($data->type === CommandOriginData::ORIGIN_DEV_CONSOLE or $data->type === CommandOriginData::ORIGIN_TEST){
			$this->putVarLong($data->varlong1);
		}
	}

	/**
	 * Decodes actor metadata from the stream.
	 *
	 * @param bool $types Whether to include metadata types along with values in the returned array
	 *
	 * @return array
	 */
	public function getActorMetadata(bool $types = true) : array{
		$count = $this->getUnsignedVarInt();
		if($count > 128){
			throw new UnexpectedValueException("Too many actor metadata: $count");
		}
		$data = [];
		for($i = 0; $i < $count; ++$i){
			$key = $this->getUnsignedVarInt();
			$type = $this->getUnsignedVarInt();
			$value = null;
			switch($type){
				case ActorMetadataTypes::BYTE:
					$value = $this->getByte();
					break;
				case ActorMetadataTypes::SHORT:
					$value = $this->getSignedLShort();
					break;
				case ActorMetadataTypes::INT:
					$value = $this->getVarInt();
					break;
				case ActorMetadataTypes::FLOAT:
					$value = $this->getLFloat();
					break;
				case ActorMetadataTypes::STRING:
					$value = $this->getString();
					break;
				case ActorMetadataTypes::NBT:
					$value = $this->getNbtCompoundRoot();
					break;
				case ActorMetadataTypes::POS:
					$this->getSignedBlockPosition($x, $y, $z);
					$value = [$x, $y, $z];
					break;
				case ActorMetadataTypes::LONG:
					$value = $this->getVarLong();
					break;
				case ActorMetadataTypes::VECTOR3F:
					$this->getVector3f($x, $y, $z);
					$value = [$x, $y, $z];
					break;
				default:
					throw new UnexpectedValueException("Invalid data type " . $type);
			}
			if($types){
				$data[$key] = [$type, $value];
			}else{
				$data[$key] = $value;
			}
		}

		return $data;
	}

	/**
	 * Writes actor metadata to the packet buffer.
	 *
	 * @param array $metadata
	 */
	public function putActorMetadata(array $metadata) : void{
		$this->putUnsignedVarInt(count($metadata));
		foreach($metadata as $key => $d){
			$this->putUnsignedVarInt($key); //data key
			$this->putUnsignedVarInt($d[0]); //data type
			switch($d[0]){
				case ActorMetadataTypes::BYTE:
					$this->putByte($d[1]);
					break;
				case ActorMetadataTypes::SHORT:
					$this->putLShort($d[1]); //SIGNED short!
					break;
				case ActorMetadataTypes::INT:
					$this->putVarInt($d[1]);
					break;
				case ActorMetadataTypes::FLOAT:
					$this->putLFloat($d[1]);
					break;
				case ActorMetadataTypes::STRING:
					$this->putString($d[1]);
					break;
				case ActorMetadataTypes::NBT:
					$this->put((new NetworkNbtSerializer())->write(new TreeRoot($d[1])));
					break;
				case ActorMetadataTypes::POS:
					$this->putSignedBlockPosition(...$d[1]);
					break;
				case ActorMetadataTypes::LONG:
					$this->putVarLong($d[1]);
					break;
				case ActorMetadataTypes::VECTOR3F:
					$this->putVector3f(...$d[1]); //x, y, z
					break;
				default:
					throw new UnexpectedValueException("Invalid data type " . $d[0]);
			}
		}
	}

	/**
	 * Reads and returns an EntityNetId
	 * @return int
	 */
	public function getEntityNetId() : int{
		return $this->getUnsignedVarInt();
	}

	/**
	 * Writes an EntityNetId
	 *
	 * @param int $entityNetId
	 */
	public function putEntityNetId(int $entityNetId) : void{
		$this->putUnsignedVarInt($entityNetId);
	}

	/**
	 * @return StructureSettings
	 *
	 * @throws BinaryDataException
	 */
	protected function getStructureSettings() : StructureSettings{
		$settings = new StructureSettings();

		$settings->paletteName = $this->getString();
		$settings->ignoreEntities = $this->getBool();
		$settings->ignoreBlocks = $this->getBool();
		$this->getBlockPosition($settings->structureSizeX, $settings->structureSizeY, $settings->structureSizeZ);
		$this->getBlockPosition($settings->structureOffsetX, $settings->structureOffsetY, $settings->structureOffsetZ);
		$settings->lastTouchedByPlayerId = $this->getActorUniqueId();
		$settings->rotation = $this->getByte();
		$settings->mirror = $this->getByte();
		$settings->integrityValue = $this->getFloat();
		$settings->integritySeed = $this->getLInt();

		return $settings;
	}

	/**
	 * @param StructureSettings $settings
	 */
	public function putStructureSettings(StructureSettings $settings) : void{
		$this->putString($settings->paletteName);
		$this->putBool($settings->ignoreEntities);
		$this->putBool($settings->ignoreBlocks);
		$this->putBlockPosition($settings->structureSizeX, $settings->structureSizeY, $settings->structureSizeZ);
		$this->putBlockPosition($settings->structureOffsetX, $settings->structureOffsetY, $settings->structureOffsetZ);
		$this->putActorUniqueId($settings->lastTouchedByPlayerId);
		$this->putByte($settings->rotation);
		$this->putByte($settings->mirror);
		$this->putFloat($settings->integrityValue);
		$this->putLInt($settings->integritySeed);
	}

	/**
	 * @return SerializedSkinImage
	 */
	public function getImage() : SerializedSkinImage{
		$width = $this->getLInt();
		$height = $this->getLInt();
		$data = $this->getString();

		return new SerializedSkinImage($width, $height, $data);
	}

	public function putImage(SerializedSkinImage $image) : void{
		$this->putLInt($image->getWidth());
		$this->putLInt($image->getHeight());
		$this->putString($image->getData());
	}

	/**
	 * @return Skin
	 */
	public function getSkin() : Skin{
		$skinId = $this->getString();
		$skinPlayFabId = $this->getString();
		$skinResourcePatch = $this->getString();
		$skinImage = $this->getImage();

		$animations = [];
		$count = $this->getLInt();
		if($count > 128){
			throw new UnexpectedValueException("Too many skin animations: $count");
		}
		for($i = 0; $i < $count; ++$i){
			$image = $this->getImage();
			$type = $this->getLInt();
			$frames = $this->getLFloat();
			$expressionType = $this->getLInt();
			$animations[] = new SkinAnimation($image, $type, $frames, $expressionType);
		}

		$capeImage = $this->getImage();
		$geometryData = $this->getString();
        $geometryDataVersion = $this->getString();
        $animationData = $this->getString();
		$capeId = $this->getString();
		$fullSkinId = $this->getString();
		$armSize = $this->getString();
		$skinColor = $this->getString();

		$personaPieces = [];
		$count = $this->getLInt();
		if($count > 128){
			throw new UnexpectedValueException("Too many persona pieces: $count");
		}
		for($i = 0; $i < $count; ++$i){
			$personaPieces[] = $this->getPersonaPiece();
		}

		$pieceTintColors = [];
		$count = $this->getLInt();
		if($count > 128){
			throw new UnexpectedValueException("Too many piece tint colors: $count");
		}
		for($i = 0; $i < $count; ++$i){
			$pieceTintColors[] = $this->getPieceTintColor();
		}

        $isPremium = $this->getBool();
        $isPersona = $this->getBool();
        $isCapeOnClassic = $this->getBool();
        $isPrimaryUser = $this->getBool();

        return new Skin($skinId, $skinPlayFabId, $skinResourcePatch, $skinImage, $animations, $capeImage, $geometryData, $animationData, $isPremium, $isPersona, $isCapeOnClassic, $capeId, $fullSkinId, $armSize, $skinColor, $personaPieces, $pieceTintColors, true, $geometryDataVersion, $isPrimaryUser);
	}

	/**
	 * @param Skin $skin
	 */
	public function putSkin(Skin $skin) : void{
		$this->putString($skin->getSkinId());
		$this->putString($skin->getPlayFabId());
		$this->putString($skin->getSkinResourcePatch());
		$this->putImage($skin->getSkinImage());

		$animations = $skin->getAnimations();
		$this->putLInt(count($animations));
		foreach($animations as $animation){
			$this->putImage($animation->getImage());
			$this->putLInt($animation->getType());
			$this->putLFloat($animation->getFrames());
			$this->putLInt($animation->getExpressionType());
		}

		$this->putImage($skin->getCapeImage());
		$this->putString($skin->getGeometryData());
        $this->putString($skin->getGeometryDataEngineVersion());
        $this->putString($skin->getAnimationData());
		$this->putString($skin->getCapeId());
		$this->putString($skin->getFullSkinId());
		$this->putString($skin->getArmSize());
		$this->putString($skin->getSkinColor());

		$this->putLInt(count($skin->getPersonaPieces()));
		foreach($skin->getPersonaPieces() as $personaPiece){
			$this->putPersonaPiece($personaPiece);
		}

		$this->putLInt(count($skin->getPieceTintColors()));
		foreach($skin->getPieceTintColors() as $pieceTintColor){
			$this->putPieceTintColor($pieceTintColor);
		}

        $this->putBool($skin->isPremium());
        $this->putBool($skin->isPersona());
        $this->putBool($skin->isCapeOnClassic());
        $this->putBool($skin->isPrimaryUser());

    }

	/**
	 * @return PersonaPiece
	 */
	public function getPersonaPiece() : PersonaPiece{
		$pieceId = $this->getString();
		$pieceType = $this->getString();
		$packId = $this->getString();
		$isDefault = $this->getBool();
		$productId = $this->getString();
		return new PersonaPiece($pieceId, $pieceType, $packId, $isDefault, $productId);
	}

	/**
	 * @param PersonaPiece $personaPiece
	 */
	public function putPersonaPiece(PersonaPiece $personaPiece) : void{
		$this->putString($personaPiece->getPieceId());
		$this->putString($personaPiece->getPieceType());
		$this->putString($personaPiece->getPackId());
		$this->putBool($personaPiece->isDefault());
		$this->putString($personaPiece->getProductId());
	}

	/**
	 * @return PieceTintColor
	 */
	public function getPieceTintColor() : PieceTintColor{
		$pieceType = $this->getString();

		$colors = [];
		$count = $this->getLInt();
		if($count > 128){
			throw new UnexpectedValueException("Too many colors in piece tint color: $count");
		}
		for($i = 0; $i < $count; ++$i){
			$colors[] = $this->getString();
		}

		return new PieceTintColor($pieceType, $colors);
	}

	public function putPieceTintColor(PieceTintColor $pieceTintColor) : void{
		$this->putString($pieceTintColor->getPieceType());
		$this->putLInt(count($pieceTintColor->getColors()));
		foreach($pieceTintColor->getColors() as $color){
			$this->putString($color);
		}
	}

	/**
	 * Reads a floating-point Vector2 object with coordinates rounded to 4 decimal places.
	 *
	 * @return Vector2
	 */
	public function getVector2() : Vector2{
		return new Vector2(
			$this->getLFloat(),
			$this->getLFloat()
		);
	}

	/**
	 * Writes a floating-point Vector2 object
	 *
	 * @param Vector2 $vector
	 */
	public function putVector2(Vector2 $vector) : void{
		$this->putLFloat($vector->x);
		$this->putLFloat($vector->y);
	}

	public function getLegacySetItemSlot() : LegacySetItemSlot{
		$data = new LegacySetItemSlot();
		$data->containerId = $this->getByte();

		for($i = 0, $count = $this->getUnsignedVarInt(); $i < $count; ++$i){
			$data->slots[] = $this->getByte();
		}
		return $data;
	}

	public function putLegacySetItemSlot(LegacySetItemSlot $setItemSlot) : void{
		$this->putByte($setItemSlot->containerId);

		$this->putUnsignedVarInt(count($setItemSlot->slots));
		foreach($setItemSlot->slots as $slot){
			$this->putByte($slot);
		}
	}

	public function getStackRequestSlotInfo() : StackRequestSlotInfo{
		$info = new StackRequestSlotInfo();
		$info->containerId = $this->getByte();
		$info->slot = $this->getByte();
		$info->stackNetworkId = $this->getVarInt();
		return $info;
	}

	public function putStackRequestSlotInfo(StackRequestSlotInfo $info) : void{
		$this->putByte($info->containerId);
		$this->putByte($info->slot);
		$this->putVarInt($info->stackNetworkId);
	}

	public function getExperiments() : Experiments{
		$experiments = [];
		$count = $this->getLInt();
		if($count > 128){
			throw new InvalidArgumentException("Too many experiments: $count");
		}
		for($i = 0; $i < $count; ++$i){
			$experimentName = $this->getString();
			$enabled = $this->getBool();
			$experiments[$experimentName] = $enabled;
		}
		$hasPreviouslyUsedExperiments = $this->getBool();
		return new Experiments($experiments, $hasPreviouslyUsedExperiments);
	}

	public function putExperiments(Experiments $experiments) : void{
		$this->putLInt(count($experiments->experiments));
		foreach($experiments->experiments as $experimentName => $enabled){
			$this->putString($experimentName);
			$this->putBool($enabled);
		}
		$this->putBool($experiments->hasPreviouslyUsedExperiments);
	}
}
