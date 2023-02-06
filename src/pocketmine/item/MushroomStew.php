<?php

declare(strict_types=1);

namespace pocketmine\item;

class MushroomStew extends Food{
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::MUSHROOM_STEW, $meta, $count, "Mushroom Stew");
	}

	public function getMaxStackSize(){
		return 1;
	}

	public function getFoodRestore() : int{
		return 6;
	}

	public function getSaturationRestore() : float{
		return 7.2;
	}

	public function getResidue(){
		return Item::get(Item::BOWL);
	}
}
