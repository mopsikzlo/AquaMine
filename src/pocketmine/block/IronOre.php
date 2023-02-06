<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\item\TieredTool;
use pocketmine\item\Tool;

class IronOre extends Solid{

	protected $id = self::IRON_ORE;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getName(){
		return "Iron Ore";
	}

	public function getToolType(){
		return Tool::TYPE_PICKAXE;
	}

	public function getToolHarvestLevel() : int{
		return TieredTool::TIER_STONE;
	}

	public function getHardness(){
		return 3;
	}

	public function getDrops(Item $item){
		if($item->isPickaxe() >= Tool::TIER_STONE){
			return [
				[Item::IRON_ORE, 0, 1],
			];
		}else{
			return [];
		}
	}
}