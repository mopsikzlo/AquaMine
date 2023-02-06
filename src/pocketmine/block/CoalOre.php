<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\item\TieredTool;
use pocketmine\item\Tool;

class CoalOre extends Solid{

	protected $id = self::COAL_ORE;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getHardness(){
		return 3;
	}

	public function getToolType(){
		return Tool::TYPE_PICKAXE;
	}

	public function getToolHarvestLevel() : int{
		return TieredTool::TIER_WOODEN;
	}

	public function getName(){
		return "Coal Ore";
	}

	public function getDrops(Item $item){
		if($item->isPickaxe() >= Tool::TIER_WOODEN){
			return [
				[Item::COAL, 0, 1],
			];
		}else{
			return [];
		}
	}

}