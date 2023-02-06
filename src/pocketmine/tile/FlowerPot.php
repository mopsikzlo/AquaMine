<?php

declare(strict_types=1);

namespace pocketmine\tile;

use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;

class FlowerPot extends Spawnable{

	public function __construct(Level $level, CompoundTag $nbt){
		if(!$nbt->hasTag("item")){
			$nbt->setShort("item", 0);
		}
		if(!$nbt->hasTag("mData")){
			$nbt->setInt("mData", 0);
		}
		parent::__construct($level, $nbt);
	}

	public function canAddItem(Item $item) : bool{
		if(!$this->isEmpty()){
			return false;
		}
		switch($item->getId()){
			/** @noinspection PhpMissingBreakStatementInspection */
			case Item::TALL_GRASS:
				if($item->getDamage() === 1){
					return false;
				}
			case Item::SAPLING:
			case Item::DEAD_BUSH:
			case Item::DANDELION:
			case Item::RED_FLOWER:
			case Item::BROWN_MUSHROOM:
			case Item::RED_MUSHROOM:
			case Item::CACTUS:
				return true;
			default:
				return false;
		}
	}

	public function getItem() : Item{
		return Item::get($this->namedtag->getShort("item"), $this->namedtag->getInt("mData"), 1);
	}

	public function setItem(Item $item){
		$this->namedtag->setShort("item", $item->getId());
		$this->namedtag->setInt("mData", $item->getDamage());
		$this->onChanged();
	}

	public function removeItem(){
		$this->setItem(Item::get(Item::AIR));
	}

	public function isEmpty() : bool{
		return $this->getItem()->getId() === Item::AIR;
	}

	public function addAdditionalSpawnData(CompoundTag $nbt, bool $isBedrock){
		$nbt->setShort("item", $this->namedtag->getShort("item"));
		$nbt->setInt("mData", $this->namedtag->getInt("mData"));
	}
}