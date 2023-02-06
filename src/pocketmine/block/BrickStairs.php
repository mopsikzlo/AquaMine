<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\TieredTool;
use pocketmine\item\Tool;

class BrickStairs extends Stair{

	protected $id = self::BRICK_STAIRS;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getHardness(){
		return 2;
	}

	public function getBlastResistance() : float{
		return 30;
	}

	public function getToolType(){
		return Tool::TYPE_PICKAXE;
	}

	public function getToolHarvestLevel() : int{
		return TieredTool::TIER_WOODEN;
	}

	public function getName(){
		return "Brick Stairs";
	}

}