<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\TieredTool;
use pocketmine\item\Tool;

class IronTrapdoor extends Trapdoor{

	protected $id = self::IRON_TRAPDOOR;

	public function getName(){
		return "Iron Trapdoor";
	}

	public function getHardness(){
		return 5;
	}

	public function getToolType(){
		return Tool::TYPE_PICKAXE;
	}

	public function getToolHarvestLevel() : int{
		return TieredTool::TIER_WOODEN;
	}
}
