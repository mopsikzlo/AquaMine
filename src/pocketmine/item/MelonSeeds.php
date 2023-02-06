<?php

declare(strict_types=1);

namespace pocketmine\item;

use pocketmine\block\Block;

class MelonSeeds extends Item{
	public function __construct($meta = 0, $count = 1){
		$this->block = Block::get(Block::MELON_STEM);
		parent::__construct(self::MELON_SEEDS, $meta, $count, "Melon Seeds");
	}
}