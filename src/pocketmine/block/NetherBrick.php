<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\item\TieredTool;
use pocketmine\item\Tool;

class NetherBrick extends Solid{

	protected $id = self::NETHER_BRICK;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getToolType(){
		return Tool::TYPE_PICKAXE;
	}

	public function getToolHarvestLevel() : int{
		return TieredTool::TIER_WOODEN;
	}

	public function getName(){
		return "Nether Bricks";
	}

	public function getHardness(){
		return 2;
	}

	public function getDrops(Item $item){
		if($item->isPickaxe() >= Tool::TIER_WOODEN){
			return [
				[Item::NETHER_BRICK, 0, 1],
			];
		}else{
			return [];
		}
	}
}