<?php

declare(strict_types=1);

namespace pocketmine\block;


class Furnace extends BurningFurnace{

	protected $id = self::FURNACE;

	public function getName(){
		return "Furnace";
	}

	public function getLightLevel(){
		return 0;
	}
}