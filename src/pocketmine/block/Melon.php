<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\item\Tool;

use function mt_rand;

class Melon extends Transparent{

	protected $id = self::MELON_BLOCK;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getName(){
		return "Melon Block";
	}

	public function getHardness(){
		return 1;
	}

	public function getToolType(){
		return Tool::TYPE_AXE;
	}

	public function getDrops(Item $item){
		return [
			[Item::MELON_SLICE, 0, mt_rand(3, 7)],
		];
	}
}