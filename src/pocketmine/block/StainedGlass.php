<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\Item;

class StainedGlass extends Glass{

	protected $id = self::STAINED_GLASS;

	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}

	public function getName() : string{
		static $names = [
			0 => "White Stained Glass",
			1 => "Orange Stained Glass",
			2 => "Magenta Stained Glass",
			3 => "Light Blue Stained Glass",
			4 => "Yellow Stained Glass",
			5 => "Lime Stained Glass",
			6 => "Pink Stained Glass",
			7 => "Gray Stained Glass",
			8 => "Light Gray Stained Glass",
			9 => "Cyan Stained Glass",
			10 => "Purple Stained Glass",
			11 => "Blue Stained Glass",
			12 => "Brown Stained Glass",
			13 => "Green Stained Glass",
			14 => "Red Stained Glass",
			15 => "Black Stained Glass",
		];
		return $names[$this->meta & 0x0f];
	}

}