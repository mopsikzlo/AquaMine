<?php


declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\item\Tool;
use pocketmine\Player;

class GlazedTerracotta extends Solid{

	public function getHardness(){
		return 1.4;
	}

	public function getToolType(){
		return Tool::TYPE_PICKAXE;
	}

	public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null){
		if($player !== null){
			$faces = [
				0 => 4,
				1 => 3,
				2 => 5,
				3 => 2
			];
			$this->meta = $faces[(~($player->getDirection() - 1)) & 0x03];
		}

		return $this->getLevel()->setBlock($block, $this, true, true);
	}

	public function getDrops(Item $item){
		if($item->isPickaxe() >= Tool::TIER_WOODEN){
			return [
				[$this->getId(), 0, 1],
			];
		}else{
			return [];
		}
	}
}