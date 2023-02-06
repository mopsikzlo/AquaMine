<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\item\TieredTool;
use pocketmine\item\Tool;

class DiamondOre extends Solid{

	protected $id = self::DIAMOND_ORE;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getHardness(){
		return 3;
	}

	public function getName(){
		return "Diamond Ore";
	}

	public function getToolType(){
		return Tool::TYPE_PICKAXE;
	}

	public function getToolHarvestLevel() : int{
		return TieredTool::TIER_IRON;
	}

	public function getDrops(Item $item){
		if($item->isPickaxe() >= Tool::TIER_IRON){
			return [
				[Item::DIAMOND, 0, 1],
			];
		}else{
			return [];
		}
	}
}