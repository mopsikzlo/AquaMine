<?php

declare(strict_types=1);

namespace pocketmine\block;

class WoodenPressurePlate extends StonePressurePlate{

	protected $id = self::WOODEN_PRESSURE_PLATE;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getName(){
		return "Wooden Pressure Plate";
	}
}
