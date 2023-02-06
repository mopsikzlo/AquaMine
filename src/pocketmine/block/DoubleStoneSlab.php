<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\item\TieredTool;
use pocketmine\item\Tool;

class DoubleStoneSlab extends Solid{

	protected $id = self::DOUBLE_STONE_SLAB;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getHardness(){
		return 2;
	}

	public function getToolType(){
		return Tool::TYPE_PICKAXE;
	}

	public function getName(){
		static $names = [
			StoneSlab::STONE => "Stone",
			StoneSlab::SANDSTONE => "Sandstone",
			StoneSlab::WOODEN => "Wooden",
			StoneSlab::COBBLESTONE => "Cobblestone",
			StoneSlab::BRICK => "Brick",
			StoneSlab::STONE_BRICK => "Stone Brick",
			StoneSlab::QUARTZ => "Quartz",
			StoneSlab::NETHER_BRICK => "Nether Brick",
		];
		return "Double " . $names[$this->meta & 0x07] . " Slab";
	}

	public function getDrops(Item $item){
		if($item->isPickaxe() >= Tool::TIER_WOODEN){
			return [
				[Item::STONE_SLAB, $this->meta & 0x07, 2],
			];
		}else{
			return [];
		}
	}

	public function getPickedItem() : Item{
		return Item::get(Item::STONE_SLAB, $this->meta & 0x07);
	}

	public function getToolHarvestLevel() : int{
		return TieredTool::TIER_WOODEN;
	}
}