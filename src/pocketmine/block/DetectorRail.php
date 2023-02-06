<?php

declare(strict_types=1);

namespace pocketmine\block;

class DetectorRail extends Rail{

	protected $id = self::DETECTOR_RAIL;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getName(){
		return "Detector Rail";
	}
}
