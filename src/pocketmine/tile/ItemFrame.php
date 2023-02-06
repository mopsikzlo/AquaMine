<?php

declare(strict_types=1);

namespace pocketmine\tile;

use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\FloatTag;

class ItemFrame extends Spawnable{

	public function __construct(Level $level, CompoundTag $nbt){
		if(!$nbt->hasTag("ItemRotation", ByteTag::class)){
			$nbt->setByte("ItemRotation", 0);
		}

		if(!$nbt->hasTag("ItemDropChance", FloatTag::class)){
			$nbt->setFloat("ItemDropChance", 1.0);
		}

		parent::__construct($level, $nbt);
	}

	public function hasItem() : bool{
		return $this->getItem()->getId() !== Item::AIR;
	}

	public function getItem() : Item{
		if($this->namedtag->hasTag("Item", CompoundTag::class)){
			return Item::nbtDeserialize($this->namedtag->getCompoundTag("Item"));
		}else{
			return Item::air();
		}
	}

	public function setItem(Item $item = null){
		if($item !== null and $item->getId() !== Item::AIR){
			$this->namedtag->setTag("Item", $item->nbtSerialize());
		}else{
			$this->namedtag->removeTag("Item");
		}
		$this->onChanged();
	}

	public function getItemRotation() : int{
		return $this->namedtag->getByte("ItemRotation");
	}

	public function setItemRotation(int $rotation){
		$this->namedtag->setByte("ItemRotation", $rotation);
		$this->onChanged();
	}

	public function getItemDropChance() : float{
		return $this->namedtag->getFloat("ItemDropChance");
	}

	public function setItemDropChance(float $chance){
		$this->namedtag->setFloat("ItemDropChance", $chance);
		$this->onChanged();
	}

	public function addAdditionalSpawnData(CompoundTag $nbt, bool $isBedrock){
		$nbt->setFloat("ItemDropChance", $this->namedtag->getFloat("ItemDropChance"));
		$nbt->setByte("ItemRotation", $this->namedtag->getByte("ItemRotation"));

		if($this->hasItem()){
			$nbt->setTag("Item", $this->namedtag->getCompoundTag("Item"));
		}
	}

}