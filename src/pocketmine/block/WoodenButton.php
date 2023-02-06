<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\Tool;

class WoodenButton extends Button{

	protected $id = self::WOODEN_BUTTON;

	public function getName(){
		return "Wooden Button";
	}

	public function getHardness(){
		return 0.5;
	}

	public function getToolType(){
		return Tool::TYPE_AXE;
	}
}
