<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\TieredTool;

class StonePressurePlate extends Transparent{

	protected $id = self::STONE_PRESSURE_PLATE;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getName(){
		return "Stone Pressure Plate";
	}

	public function isSolid(){
		return false;
	}

	public function getHardness(){
		return 0.5;
	}

	public function getToolType() : int{
		return BlockToolType::TYPE_PICKAXE;
	}

	public function getToolHarvestLevel() : int{
		return TieredTool::TIER_WOODEN;
	}
}
