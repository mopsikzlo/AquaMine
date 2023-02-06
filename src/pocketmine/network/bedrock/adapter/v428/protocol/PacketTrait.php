<?php

/*
 *
 *									 __  __ _
 *	  /\						  |  \/  (_)
 *	 /  \	__ _ _	_  __ _| \  / |_ _ __	___
 *	/ /\ \ / _` | | | |/ _` | |\/| | | '_ \ / _ \
 *  / ____ \ (_| | |_| | (_| | |  | | | | | |  __/
 * /_/	 \_\__, |\__,_|\__,_|_|  |_|_|_| |_|\___|
 *				 | |
 *				 |_|
 *
 * This program is private software. No license required.
 * Publication of this program is forbidden and will be punished.
 *
 * @author GreenWix Project
 * @link https://www.greenwix.fun
 *
 *
*/

declare(strict_types=1);

namespace pocketmine\network\bedrock\adapter\v428\protocol;

use pocketmine\item\Durable;
use pocketmine\item\Item;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\LongTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\nbt\TreeRoot;
use pocketmine\network\bedrock\adapter\v431\palette\ItemPalette as ItemPalette431;
use pocketmine\network\bedrock\protocol\types\inventory\ItemInstance;
use pocketmine\network\mcpe\NetworkNbtSerializer;
use UnexpectedValueException;

trait PacketTrait{

	public function getItemStackWithoutStackId() : Item{
		$netId = $this->getVarInt();
		if($netId === 0){
			return Item::air();
		}

		$auxValue = $this->getVarInt();
		$netData = $auxValue >> 8;
		$cnt = $auxValue & 0xff;

		[$id, $meta] = ItemPalette431::getLegacyFromRuntimeId($netId, $netData);

		$nbtLen = $this->getLShort();

		$nbt = null;
		if($nbtLen === 0xffff){
			$c = $this->getByte();
			if($c !== 1){
				throw new UnexpectedValueException("Unexpected NBT data version $c");
			}

			$nbt = $this->getNbtCompoundRoot();
			if($nbt->hasTag(self::DAMAGE_TAG, IntTag::class)){ //a hack: 1.12+ meta format
				$meta = $nbt->getInt(self::DAMAGE_TAG);
				$nbt->removeTag(self::DAMAGE_TAG);
			}

			if($nbt->hasTag(self::DAMAGE_TAG_CONFLICT_RESOLUTION)){
				$nbt->setTag(self::DAMAGE_TAG, $nbt->getTag(self::DAMAGE_TAG_CONFLICT_RESOLUTION));
				$nbt->removeTag(self::DAMAGE_TAG_CONFLICT_RESOLUTION);
			}

			if($nbt->hasTag("map_uuid", LongTag::class)){ // 1.1 compatibility hack
				$nbt->setString("map_uuid", (string) $nbt->getLong("map_uuid"));
			}
		}elseif($nbtLen !== 0){
			throw new UnexpectedValueException("Unexpected fake NBT length $nbtLen");
		}

		//TODO
		$canPlaceOn = $this->getVarInt();
		if($canPlaceOn > 128){
			throw new UnexpectedValueException("Too many canPlaceOn: $canPlaceOn");
		}elseif($canPlaceOn > 0){
			for($i = 0; $i < $canPlaceOn; ++$i){
				$this->getString();
			}
		}

		//TODO
		$canDestroy = $this->getVarInt();
		if($canDestroy > 128){
			throw new UnexpectedValueException("Too many canDestroy: $canDestroy");
		}elseif($canDestroy > 0){
			for($i = 0; $i < $canDestroy; ++$i){
				$this->getString();
			}
		}

		if($netId === ItemPalette431::getRuntimeFromStringId("minecraft:shield")){ //SHIELD
			$this->getVarLong(); //"blocking tick" (ffs mojang)
		}

		return Item::get($id, $meta, $cnt, $nbt);
	 }

	 public function putItemStackWithoutStackId(Item $item) : void{
		if($item->isNull()){
			$this->putVarInt(0);
			return;
		}

		if(!$item instanceof Durable){
			[$netId, $netData] = ItemPalette431::getRuntimeFromLegacyId($item->getId(), $item->getDamage());

			$this->putVarInt($netId);
			$auxValue = (($netData & 0x7fff) << 8) | $item->getCount();
			$this->putVarInt($auxValue);
		}else{
			[$netId, ] = ItemPalette431::getRuntimeFromLegacyId($item->getId(), 0);

			$this->putVarInt($netId);
			$this->putVarInt($item->getCount());
		}

		if($item->hasCompoundTag() or ($item instanceof Durable and $item->getDamage() !== 0)){
			$this->putLShort(0xffff);
			$this->putByte(1); //TODO: NBT data version (?)

			$nbt = clone $item->getNamedTag();
			if($item instanceof Durable and $item->getDamage() !== 0){
				if($nbt->hasTag(self::DAMAGE_TAG)){
					$nbt->setTag(self::DAMAGE_TAG_CONFLICT_RESOLUTION, $nbt->getTag(self::DAMAGE_TAG));
				}

				$nbt->setInt(self::DAMAGE_TAG, $item->getDamage() & 0x7fff); //a hack: 1.12+ meta format
			}
			if($nbt->hasTag("map_uuid", StringTag::class)){ // 1.1 compatibility
				$nbt->setLong("map_uuid", (int) $nbt->getString("map_uuid"));
			}

			$this->put((new NetworkNbtSerializer())->write(new TreeRoot($nbt)));
		}else{
			$this->putLShort(0);
		}

		$this->putVarInt(0); //CanPlaceOn entry count (TODO)
		$this->putVarInt(0); //CanDestroy entry count (TODO)

		if($netId === ItemPalette431::getRuntimeFromStringId("minecraft:shield")){ //SHIELD
			$this->putVarLong(0); //"blocking tick" (ffs mojang)
		}
	 }

	/**
	 * @param Item|ItemInstance $item
	 */
	 public function putItemInstance($item) : void{
		if($item instanceof ItemInstance){
			$item = $item->stack;
		}

	 	$this->putItemStackWithoutStackId($item);
	}

	public function getItemInstance() : ItemInstance{
			return ItemInstance::legacy($this->getItemStackWithoutStackId());
	}
}