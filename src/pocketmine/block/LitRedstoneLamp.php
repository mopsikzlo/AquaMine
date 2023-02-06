<?php

declare(strict_types=1);

namespace pocketmine\block;

class LitRedstoneLamp extends RedstoneLamp{

	protected $id = self::LIT_REDSTONE_LAMP;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getName(){
		return "Lit Redstone Lamp";
	}
}
