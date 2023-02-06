<?php

declare(strict_types=1);

namespace pocketmine\block;


use pocketmine\item\TieredTool;

class GlowingObsidian extends Solid{

	protected $id = self::GLOWING_OBSIDIAN;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getName(){
		return "Glowing Obsidian";
	}

	public function getLightLevel(){
		return 12;
	}

	public function getToolType() : int{
		return BlockToolType::TYPE_PICKAXE;
	}

	public function getToolHarvestLevel() : int{
		return TieredTool::TIER_DIAMOND;
	}
}