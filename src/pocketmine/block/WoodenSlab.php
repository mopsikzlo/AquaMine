<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\item\Tool;

class WoodenSlab extends Slab{

	protected $id = self::WOODEN_SLAB;

	protected $doubleId = self::DOUBLE_WOODEN_SLAB;

	public function getHardness(){
		return 2;
	}

	public function getName(){
		static $names = [
			0 => "Oak",
			1 => "Spruce",
			2 => "Birch",
			3 => "Jungle",
			4 => "Acacia",
			5 => "Dark Oak",
			6 => "",
			7 => ""
		];
		return (($this->meta & 0x08) === 0x08 ? "Upper " : "") . $names[$this->meta & 0x07] . " Wooden Slab";
	}

	public function getToolType(){
		return Tool::TYPE_AXE;
	}

	public function getDrops(Item $item){
		return [
			[$this->id, $this->meta & 0x07, 1],
		];
	}
}