<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\TieredTool;
use pocketmine\item\Tool;

class NetherBrickStairs extends Stair{

	protected $id = self::NETHER_BRICK_STAIRS;

	public function getName(){
		return "Nether Brick Stairs";
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

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

}