<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\item\TieredTool;
use pocketmine\item\Tool;

class Stone extends Solid{
	public const NORMAL = 0;
	public const GRANITE = 1;
	public const POLISHED_GRANITE = 2;
	public const DIORITE = 3;
	public const POLISHED_DIORITE = 4;
	public const ANDESITE = 5;
	public const POLISHED_ANDESITE = 6;

	protected $id = self::STONE;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getHardness(){
		return 1.5;
	}

	public function getToolType(){
		return Tool::TYPE_PICKAXE;
	}

	public function getToolHarvestLevel() : int{
		return TieredTool::TIER_WOODEN;
	}

	public function getName(){
		static $names = [
			self::NORMAL => "Stone",
			self::GRANITE => "Granite",
			self::POLISHED_GRANITE => "Polished Granite",
			self::DIORITE => "Diorite",
			self::POLISHED_DIORITE => "Polished Diorite",
			self::ANDESITE => "Andesite",
			self::POLISHED_ANDESITE => "Polished Andesite",
			7 => "Unknown Stone",
		];
		return $names[$this->meta & 0x07];
	}

	public function getDrops(Item $item){
		if($item->isPickaxe() >= Tool::TIER_WOODEN){
			return [
				[$this->getDamage() === 0 ? Item::COBBLESTONE : Item::STONE, $this->getDamage(), 1],
			];
		}else{
			return [];
		}
	}

}