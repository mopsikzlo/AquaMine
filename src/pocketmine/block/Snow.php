<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\TieredTool;
use pocketmine\item\Tool;

class Snow extends Solid{

	protected $id = self::SNOW_BLOCK;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getHardness(){
		return 0.2;
	}

	public function getToolType(){
		return Tool::TYPE_SHOVEL;
	}

	public function getToolHarvestLevel() : int{
		return TieredTool::TIER_WOODEN;
	}

	public function getName(){
		return "Snow Block";
	}

}