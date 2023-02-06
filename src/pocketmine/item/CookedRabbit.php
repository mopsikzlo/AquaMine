<?php

declare(strict_types=1);

namespace pocketmine\item;

class CookedRabbit extends Food{
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::COOKED_RABBIT, $meta, $count, "Cooked Rabbit");
	}

	public function getFoodRestore() : int{
		return 5;
	}

	public function getSaturationRestore() : float{
		return 6;
	}
}
