<?php

namespace pocketmine\network\bedrock\adapter\v448\protocol;


use pocketmine\block\BlockIds;
use pocketmine\item\Durable;
use pocketmine\item\Item;
use pocketmine\nbt\LittleEndianNbtSerializer;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\LongTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\nbt\TreeRoot;
use pocketmine\network\bedrock\adapter\v448\palette\BlockPalette as BlockPalette448;
use pocketmine\network\bedrock\adapter\v448\palette\ItemPalette as ItemPalette448;
use pocketmine\network\bedrock\protocol\types\skin\Skin;
use pocketmine\network\bedrock\protocol\types\skin\SkinAnimation;
use pocketmine\network\mcpe\NetworkBinaryStream;
use UnexpectedValueException;

trait PacketTrait {

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
        $animationData = $this->getString();
        $isPremium = $this->getBool();
        $isPersona = $this->getBool();
        $isCapeOnClassic = $this->getBool();
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

        return new Skin($skinId, $skinPlayFabId, $skinResourcePatch, $skinImage, $animations, $capeImage, $geometryData, $animationData, $isPremium, $isPersona, $isCapeOnClassic, $capeId, $fullSkinId, $armSize, $skinColor, $personaPieces, $pieceTintColors);
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
        $this->putString($skin->getAnimationData());
        $this->putBool($skin->isPremium());
        $this->putBool($skin->isPersona());
        $this->putBool($skin->isCapeOnClassic());
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
    }

    public function getItemStack(\Closure $readExtraCrapInTheMiddle) : Item{
        $netId = $this->getVarInt();
        if($netId === 0){
            return Item::air();
        }

        $cnt = $this->getLShort();
        $netData = $this->getUnsignedVarInt();

        [$id, $meta] = ItemPalette448::getLegacyFromRuntimeId($netId, $netData);

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

            if($netId === ItemPalette448::getRuntimeFromStringId("minecraft:shield")){ //SHIELD
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
        [$netId, $netData] = ItemPalette448::getRuntimeFromLegacyId($item->getId(),$item instanceof Durable ? 0 : $item->getDamage());

        $this->putVarInt($netId);
        $this->putLShort($item->getCount());
        $this->putUnsignedVarInt($netData);

        $writeExtraCrapInTheMiddle($this);

        $blockRuntimeId = 0;
        $isBlockItem = $item->getId() < 256;
        if($isBlockItem){
            $block = $item->getBlock();
            if($block->getId() !== BlockIds::AIR){
                $blockRuntimeId = BlockPalette448::getRuntimeFromLegacyId($block->getId(), $block->getDamage());
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

            if($netId === ItemPalette448::getRuntimeFromStringId("minecraft:shield")){ //SHIELD
                $this->putLLong(0); //"blocking tick" (ffs mojang)
            }
            return $extraData->buffer;
        })());
    }
}