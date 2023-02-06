<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\item\TieredTool;
use pocketmine\item\Tool;

class EndStoneBricks extends Solid{

	protected $id = self::END_BRICKS;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getName(){
		return "End Stone Bricks";
	}

	public function getHardness(){
		return 0.8;
	}

	public function getToolType() {
		return Tool::TYPE_PICKAXE;
	}

	public function getToolHarvestLevel() : int{
		return TieredTool::TIER_WOODEN;
	}

	public function getDrops(Item $item){
		if($item->isPickaxe() >= Tool::TIER_WOODEN){
			return parent::getDrops($item);
		}

		return [];
	}

}