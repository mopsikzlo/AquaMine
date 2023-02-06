<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\item\Tool;

class StoneSlab extends Slab{
	public const STONE = 0;
	public const SANDSTONE = 1;
	public const WOODEN = 2;
	public const COBBLESTONE = 3;
	public const BRICK = 4;
	public const STONE_BRICK = 5;
	public const QUARTZ = 6;
	public const NETHER_BRICK = 7;

	protected $id = self::STONE_SLAB;

	protected $doubleId = self::DOUBLE_STONE_SLAB;

	public function getHardness(){
		return 2;
	}

	public function getName(){
		static $names = [
			self::STONE => "Stone",
			self::SANDSTONE => "Sandstone",
			self::WOODEN => "Wooden",
			self::COBBLESTONE => "Cobblestone",
			self::BRICK => "Brick",
			self::STONE_BRICK => "Stone Brick",
			self::QUARTZ => "Quartz",
			self::NETHER_BRICK => "Nether Brick",
		];
		return (($this->meta & 0x08) > 0 ? "Upper " : "") . $names[$this->meta & 0x07] . " Slab";
	}

	public function getToolType(){
		return Tool::TYPE_PICKAXE;
	}

	public function getDrops(Item $item){
		if($item->isPickaxe() >= Tool::TIER_WOODEN){
			return [
				[$this->id, $this->meta & 0x07, 1],
			];
		}else{
			return [];
		}
	}
}