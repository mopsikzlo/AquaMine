<?php

declare(strict_types=1);

namespace pocketmine\block;

class Lever extends Flowable{

	protected $id = self::LEVER;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getName(){
		return "Lever";
	}
}
