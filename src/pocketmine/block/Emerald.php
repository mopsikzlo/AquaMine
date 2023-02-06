<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\item\TieredTool;
use pocketmine\item\Tool;

class Emerald extends Solid{

	protected $id = self::EMERALD_BLOCK;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getHardness(){
		return 5;
	}

	public function getToolType(){
		return Tool::TYPE_PICKAXE;
	}

	public function getToolHarvestLevel() : int{
		return TieredTool::TIER_IRON;
	}

	public function getName(){
		return "Emerald Block";
	}

	public function getDrops(Item $item){
		if($item->isPickaxe() >= Tool::TIER_IRON){
			return [
				[Item::EMERALD_BLOCK, 0, 1],
			];
		}else{
			return [];
		}
	}
}