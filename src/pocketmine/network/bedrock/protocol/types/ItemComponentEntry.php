<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol\types;

use pocketmine\nbt\tag\CompoundTag;

class ItemComponentEntry{

	/** @var string */
	public $name;
	/** @var CompoundTag */
	public $tag;

	public function __construct(string $name, CompoundTag $tag){
		$this->name = $name;
		$this->tag = $tag;
	}
}