<?php

declare(strict_types=1);

namespace pocketmine\item;

use pocketmine\block\Block;

class WheatSeeds extends Item{
	public function __construct($meta = 0, $count = 1){
		$this->block = Block::get(Block::WHEAT_BLOCK);
		parent::__construct(self::WHEAT_SEEDS, $meta, $count, "Wheat Seeds");
	}
}