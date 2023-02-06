<?php

declare(strict_types=1);

namespace pocketmine\item;

use pocketmine\block\Block;

class ItemFrame extends Item{
	public function __construct($meta = 0, $count = 1){
		$this->block = Block::get(Block::ITEM_FRAME_BLOCK);
		parent::__construct(self::ITEM_FRAME, $meta, $count, "Item Frame");
	}
}