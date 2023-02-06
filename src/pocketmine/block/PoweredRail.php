<?php

declare(strict_types=1);

namespace pocketmine\block;

class PoweredRail extends Rail{
	protected $id = self::POWERED_RAIL;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getName(){
		return "Powered Rail";
	}
}
