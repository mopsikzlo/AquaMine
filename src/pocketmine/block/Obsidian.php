<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\item\TieredTool;
use pocketmine\item\Tool;

class Obsidian extends Solid{

	protected $id = self::OBSIDIAN;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getName(){
		return "Obsidian";
	}

	public function getToolType(){
		return Tool::TYPE_PICKAXE;
	}

	public function getHardness(){
		return 35;
	}

	public function getDrops(Item $item){
		if($item->isPickaxe() >= Tool::TIER_DIAMOND){
			return [
				[Item::OBSIDIAN, 0, 1],
			];
		}else{
			return [];
		}
	}

	public function getToolHarvestLevel() : int{
		return TieredTool::TIER_DIAMOND;
	}
}