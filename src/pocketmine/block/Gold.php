<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\item\TieredTool;
use pocketmine\item\Tool;

class Gold extends Solid{

	protected $id = self::GOLD_BLOCK;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getName(){
		return "Gold Block";
	}

	public function getHardness(){
		return 3;
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
				[Item::GOLD_BLOCK, 0, 1],
			];
		}else{
			return [];
		}
	}
}