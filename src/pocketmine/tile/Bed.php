<?php

declare(strict_types=1);

namespace pocketmine\tile;


use pocketmine\level\Level;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;

class Bed extends Spawnable{

	public function __construct(Level $level, CompoundTag $nbt){
		if(!$nbt->hasTag("color", ByteTag::class)){
			$nbt->setByte("color", 14); //default to old red
		}
		parent::__construct($level, $nbt);
	}

	public function getColor() : int{
		return $this->namedtag->getByte("color");
	}

	public function setColor(int $color){
		$this->namedtag->setByte("color", $color & 0x0f);
		$this->onChanged();
	}

	public function addAdditionalSpawnData(CompoundTag $nbt, bool $isBedrock){
		$nbt->setByte("color", $this->getColor());
	}
}