<?php

declare(strict_types=1);

namespace pocketmine\tile;

use pocketmine\level\Level;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;

class Skull extends Spawnable{
	public const TYPE_SKELETON = 0;
	public const TYPE_WITHER = 1;
	public const TYPE_ZOMBIE = 2;
	public const TYPE_HUMAN = 3;
	public const TYPE_CREEPER = 4;
	public const TYPE_DRAGON = 5;

	public function __construct(Level $level, CompoundTag $nbt){
		if(!$nbt->hasTag("SkullType", ByteTag::class)){
			$nbt->setByte("SkullType", 0);
		}
		if(!$nbt->hasTag("Rot", ByteTag::class)){
			$nbt->setByte("Rot", 0);
		}
		parent::__construct($level, $nbt);
	}

	public function setType(int $type){
		$this->namedtag->setByte("SkullType", $type);
		$this->onChanged();
	}

	public function getType() : int{
		return $this->namedtag->getByte("SkullType");
	}

	public function addAdditionalSpawnData(CompoundTag $nbt, bool $isBedrock){
		$nbt->setByte("SkullType", $this->namedtag->getByte("SkullType"));
		$nbt->setByte("Rot", $this->namedtag->getByte("Rot"));
	}
}