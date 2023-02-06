<?php

declare(strict_types=1);

namespace pocketmine\item;

use pocketmine\block\Block;

class WoodenDoor extends Item{
	public function __construct($meta = 0, $count = 1){
		$this->block = Block::get(Block::WOODEN_DOOR_BLOCK);
		parent::__construct(self::WOODEN_DOOR, $meta, $count, "Wooden Door");
	}

	public function getMaxStackSize(){
		return 1;
	}
}