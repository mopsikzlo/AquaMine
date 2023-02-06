<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\Item;

class CocoaBlock extends Solid{

	protected $id = self::COCOA_BLOCK;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getName(){
		return "Cocoa Block";
	}

	public function getPickedItem() : Item{
		return Item::get(Item::DYE, 3);
	}
}
