<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\item\Tool;

class WoodenStairs extends Stair{

	public function getHardness(){
		return 2;
	}

	public function getBlastResistance() : float{
		return 15;
	}

	public function getToolType(){
		return Tool::TYPE_AXE;
	}

	public function getDrops(Item $item){
		return [
			[$this->id, 0, 1],
		];
	}
}