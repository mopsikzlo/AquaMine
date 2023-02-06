<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\item\TieredTool;
use pocketmine\item\Tool;

class EmeraldOre extends Solid{

	protected $id = self::EMERALD_ORE;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getName(){
		return "Emerald Ore";
	}

	public function getToolType(){
		return Tool::TYPE_PICKAXE;
	}

	public function getHardness(){
		return 3;
	}

	public function getToolHarvestLevel() : int{
		return TieredTool::TIER_IRON;
	}

	public function getDrops(Item $item){
		if($item->isPickaxe() >= Tool::TIER_IRON){
			return [
				[Item::EMERALD, 0, 1],
			];
		}else{
			return [];
		}
	}
}