<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\TieredTool;
use pocketmine\item\Tool;

class HardenedClay extends Solid{

	protected $id = self::HARDENED_CLAY;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getName(){
		return "Hardened Clay";
	}

	public function getToolType(){
		return Tool::TYPE_PICKAXE;
	}

	public function getHardness(){
		return 1.25;
	}

	public function getToolHarvestLevel() : int{
		return TieredTool::TIER_WOODEN;
	}
}