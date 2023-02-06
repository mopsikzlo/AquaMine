<?php

declare(strict_types=1);

namespace pocketmine\item;

use pocketmine\block\Block;

class Potato extends Food{
	public function __construct($meta = 0, $count = 1){
		$this->block = Block::get(Block::POTATO_BLOCK);
		parent::__construct(self::POTATO, $meta, $count, "Potato");
	}

	public function getFoodRestore() : int{
		return 1;
	}

	public function getSaturationRestore() : float{
		return 0.6;
	}
}