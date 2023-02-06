<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\Tool;

class BrewingStand extends Transparent{

	protected $id = self::BREWING_STAND_BLOCK;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getName(){
		return "Brewing Stand";
	}

	public function getHardness(){
		return 0.5;
	}

	public function getToolType(){
		return Tool::TYPE_PICKAXE;
	}
}