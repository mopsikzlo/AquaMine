<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\Item;

class StainedGlassPane extends GlassPane{

	protected $id = self::STAINED_GLASS_PANE;

	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}

	public function getName() : string{
		static $names = [
			0 => "White Stained Glass Pane",
			1 => "Orange Stained Glass Pane",
			2 => "Magenta Stained Glass Pane",
			3 => "Light Blue Stained Glass Pane",
			4 => "Yellow Stained Glass Pane",
			5 => "Lime Stained Glass Pane",
			6 => "Pink Stained Glass Pane",
			7 => "Gray Stained Glass Pane",
			8 => "Light Gray Stained Glass Pane",
			9 => "Cyan Stained Glass Pane",
			10 => "Purple Stained Glass Pane",
			11 => "Blue Stained Glass Pane",
			12 => "Brown Stained Glass Pane",
			13 => "Green Stained Glass Pane",
			14 => "Red Stained Glass Pane",
			15 => "Black Stained Glass Pane",
		];
		return $names[$this->meta & 0x0f];
	}
}