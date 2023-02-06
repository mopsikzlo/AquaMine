<?php

declare(strict_types=1);

namespace pocketmine\block;

class DaylightSensor extends Transparent{

	protected $id = self::DAYLIGHT_SENSOR;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getName(){
		return "Daylight Sensor";
	}

	public function getHardness(){
		return 0.2;
	}
}
