<?php

declare(strict_types=1);

namespace pocketmine\item;

class Egg extends ProjectileItem{
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::EGG, $meta, $count, "Egg");
	}

	public function getProjectileEntityType() : string{
		return "Egg";
	}

	public function getThrowForce() : float{
		return 1.5;
	}
}

