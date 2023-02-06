<?php

declare(strict_types=1);

namespace pocketmine\block;

class ActivatorRail extends Rail{

	protected $id = self::ACTIVATOR_RAIL;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getName(){
		return "Activator Rail";
	}
}
