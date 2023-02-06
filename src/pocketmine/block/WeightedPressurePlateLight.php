<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\TieredTool;

class WeightedPressurePlateLight extends Transparent{

	protected $id = self::LIGHT_WEIGHTED_PRESSURE_PLATE;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getName(){
		return "Weighted Pressure Plate Light";
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
