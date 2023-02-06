<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\Tool;

class StoneButton extends Button{

	protected $id = self::STONE_BUTTON;

	public function getName(){
		return "Stone Button";
	}

	public function getHardness(){
		return 0.5;
	}

	public function getToolType(){
		return Tool::TYPE_PICKAXE;
	}
}
