<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\item\TieredTool;
use pocketmine\item\Tool;

class Iron extends Solid{

	protected $id = self::IRON_BLOCK;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getName(){
		return "Iron Block";
	}

	public function getToolType(){
		return Tool::TYPE_PICKAXE;
	}

	public function getToolHarvestLevel() : int{
		return TieredTool::TIER_STONE;
	}

	public function getHardness(){
		return 5;
	}

	public function getDrops(Item $item){
		if($item->isPickaxe() >= Tool::TIER_STONE){
			return [
				[Item::IRON_BLOCK, 0, 1],
			];
		}else{
			return [];
		}
	}
}