<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\Item;

class Bedrock extends Solid{

	protected $id = self::BEDROCK;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getName(){
		return "Bedrock";
	}

	public function getHardness(){
		return -1;
	}

	public function getBlastResistance() : float{
		return 18000000;
	}

	public function isBreakable(Item $item){
		return false;
	}
}