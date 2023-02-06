<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\TieredTool;
use pocketmine\item\Tool;

class CobblestoneStairs extends Stair{

	protected $id = self::COBBLESTONE_STAIRS;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getHardness(){
		return 2;
	}

	public function getToolType(){
		return Tool::TYPE_PICKAXE;
	}

	public function getToolHarvestLevel() : int{
		return TieredTool::TIER_WOODEN;
	}

	public function getName(){
		return "Cobblestone Stairs";
	}

}