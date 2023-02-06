<?php

declare(strict_types=1);

namespace pocketmine\block;

class StillWater extends Water{

	protected $id = self::STILL_WATER;

	public function getName(){
		return "Still Water";
	}
}