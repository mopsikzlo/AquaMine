<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\Tool;

class Podzol extends Solid{

	protected $id = self::PODZOL;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getToolType(){
		return Tool::TYPE_SHOVEL;
	}

	public function getName(){
		return "Podzol";
	}

	public function getHardness(){
		return 2.5;
	}
}