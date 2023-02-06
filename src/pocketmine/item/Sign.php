<?php

declare(strict_types=1);

namespace pocketmine\item;

use pocketmine\block\Block;

class Sign extends Item{
	public function __construct($meta = 0, $count = 1){
		$this->block = Block::get(Block::SIGN_POST);
		parent::__construct(self::SIGN, $meta, $count, "Sign");
	}

	public function getMaxStackSize(){
		return 16;
	}
}