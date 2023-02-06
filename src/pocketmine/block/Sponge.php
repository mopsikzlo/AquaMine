<?php

declare(strict_types=1);

namespace pocketmine\block;


class Sponge extends Solid{

	protected $id = self::SPONGE;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getHardness(){
		return 0.6;
	}

	public function getName(){
		return "Sponge";
	}

}