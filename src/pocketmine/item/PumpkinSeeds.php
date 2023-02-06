<?php

declare(strict_types=1);

namespace pocketmine\item;

use pocketmine\block\Block;

class PumpkinSeeds extends Item{
	public function __construct($meta = 0, $count = 1){
		$this->block = Block::get(Block::PUMPKIN_STEM);
		parent::__construct(self::PUMPKIN_SEEDS, $meta, $count, "Pumpkin Seeds");
	}
}