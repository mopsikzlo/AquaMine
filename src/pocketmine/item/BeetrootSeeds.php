<?php

declare(strict_types=1);

namespace pocketmine\item;

use pocketmine\block\Block;

class BeetrootSeeds extends Item{
	public function __construct($meta = 0, $count = 1){
		$this->block = Block::get(Block::BEETROOT_BLOCK);
		parent::__construct(self::BEETROOT_SEEDS, $meta, $count, "Beetroot Seeds");
	}
}