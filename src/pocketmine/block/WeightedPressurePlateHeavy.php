<?php

declare(strict_types=1);

namespace pocketmine\block;

class WeightedPressurePlateHeavy extends WeightedPressurePlateLight{

	protected $id = self::HEAVY_WEIGHTED_PRESSURE_PLATE;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getName(){
		return "Weighted Pressure Plate Heavy";
	}
}
