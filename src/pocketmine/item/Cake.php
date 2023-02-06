<?php

declare(strict_types=1);

namespace pocketmine\item;

use pocketmine\block\Block;

class Cake extends Item{
	public function __construct($meta = 0, $count = 1){
		$this->block = Block::get(Block::CAKE_BLOCK);
		parent::__construct(self::CAKE, $meta, $count, "Cake");
	}

	public function getMaxStackSize(){
		return 1;
	}
}