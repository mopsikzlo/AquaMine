<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\item\TieredTool;
use pocketmine\item\Tool;
use pocketmine\level\Level;
use pocketmine\Player;

use function mt_rand;

class RedstoneOre extends Solid{

	protected $id = self::REDSTONE_ORE;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getName(){
		return "Redstone Ore";
	}

	public function getHardness(){
		return 3;
	}

	public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null){
		return $this->getLevel()->setBlock($this, $this, true, false);
	}

	public function onUpdate($type){
		if($type === Level::BLOCK_UPDATE_NORMAL or $type === Level::BLOCK_UPDATE_TOUCH){
			$this->getLevel()->setBlock($this, Block::get(Block::GLOWING_REDSTONE_ORE, $this->meta));

			return Level::BLOCK_UPDATE_WEAK;
		}

		return false;
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
				[Item::REDSTONE_DUST, 0, mt_rand(4, 5)],
			];
		}else{
			return [];
		}
	}
}