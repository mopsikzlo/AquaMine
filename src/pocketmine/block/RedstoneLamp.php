<?php

declare(strict_types=1);

namespace pocketmine\block;

class RedstoneLamp extends Solid{

	protected $id = self::REDSTONE_LAMP;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getName(){
		return "Redstone Lamp";
	}
}
