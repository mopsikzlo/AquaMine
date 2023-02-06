<?php

declare(strict_types=1);

namespace pocketmine\item;


class Snowball extends ProjectileItem{
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::SNOWBALL, $meta, $count, "Snowball");
	}

	public function getMaxStackSize(){
		return 16;
	}

	public function getProjectileEntityType() : string{
		return "Snowball";
	}

	public function getThrowForce() : float{
		return 1.5;
	}
}