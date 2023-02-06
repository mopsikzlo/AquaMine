<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\item\TieredTool;
use pocketmine\item\Tool;

class Quartz extends Solid{

	public const QUARTZ_NORMAL = 0;
	public const QUARTZ_CHISELED = 1;
	public const QUARTZ_PILLAR = 2;
	public const QUARTZ_PILLAR2 = 3;

	protected $id = self::QUARTZ_BLOCK;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getHardness(){
		return 0.8;
	}

	public function getName(){
		static $names = [
			self::QUARTZ_NORMAL => "Quartz Block",
			self::QUARTZ_CHISELED => "Chiseled Quartz Block",
			self::QUARTZ_PILLAR => "Quartz Pillar",
			self::QUARTZ_PILLAR2 => "Quartz Pillar",
		];
		return $names[$this->meta & 0x03];
	}

	public function getToolType(){
		return Tool::TYPE_PICKAXE;
	}

	public function getToolHarvestLevel() : int{
		return TieredTool::TIER_WOODEN;
	}

	public function getDrops(Item $item){
		if($item->isPickaxe() >= Tool::TIER_WOODEN){
			return [
				[Item::QUARTZ_BLOCK, $this->meta & 0x03, 1],
			];
		}else{
			return [];
		}
	}
}