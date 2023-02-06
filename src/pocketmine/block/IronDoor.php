<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\item\TieredTool;
use pocketmine\item\Tool;

class IronDoor extends Door{

	protected $id = self::IRON_DOOR_BLOCK;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getName(){
		return "Iron Door Block";
	}

	public function getToolType(){
		return Tool::TYPE_PICKAXE;
	}

	public function getToolHarvestLevel() : int{
		return TieredTool::TIER_WOODEN;
	}

	public function getHardness(){
		return 5;
	}

	public function getDrops(Item $item){
		if($item->isPickaxe() >= Tool::TIER_WOODEN){
			return [
				[Item::IRON_DOOR, 0, 1],
			];
		}else{
			return [];
		}
	}
}