<?php

declare(strict_types=1);

namespace pocketmine\item;

use pocketmine\block\Block;

class Sugarcane extends Item{
	public function __construct($meta = 0, $count = 1){
		$this->block = Block::get(Block::SUGARCANE_BLOCK);
		parent::__construct(self::SUGARCANE, $meta, $count, "Sugar Cane");
	}
}