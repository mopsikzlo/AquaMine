<?php

declare(strict_types=1);

namespace pocketmine\item;

class Cookie extends Food{
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::COOKIE, $meta, $count, "Cookie");
	}

	public function getFoodRestore() : int{
		return 2;
	}

	public function getSaturationRestore() : float{
		return 0.4;
	}
}

