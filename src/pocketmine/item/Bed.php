<?php

declare(strict_types=1);

namespace pocketmine\item;

use pocketmine\block\Block;

class Bed extends Item{
	public function __construct($meta = 0, $count = 1){
		$this->block = Block::get(Block::BED_BLOCK);
		parent::__construct(self::BED, $meta, $count, "Bed");
	}

	public function getMaxStackSize(){
		return 1;
	}
}