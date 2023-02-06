<?php

declare(strict_types=1);

namespace pocketmine\tile;

use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;

class EnchantTable extends Spawnable implements Nameable{


	public function getName() : string{
		return $this->hasName() ? $this->namedtag->getString("CustomName") : "Enchanting Table";
	}

	public function hasName() : bool{
		return $this->namedtag->hasTag("CustomName", StringTag::class);
	}

	public function setName(string $str){
		if($str === ""){
			$this->namedtag->removeTag("CustomName");
			return;
		}

		$this->namedtag->setString("CustomName", $str);
	}

	public function addAdditionalSpawnData(CompoundTag $nbt, bool $isBedrock){
		if($this->hasName()){
			$nbt->setString("CustomName", $this->namedtag->getString("CustomName"));
		}
	}
}
