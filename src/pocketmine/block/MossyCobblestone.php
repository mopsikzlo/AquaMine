<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\item\Tool;

class MossyCobblestone extends Solid{

	protected $id = self::MOSSY_COBBLESTONE;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getName(){
		return "Moss Stone";
	}

	public function getHardness(){
		return 2;
	}

	public function getToolType(){
		return Tool::TYPE_PICKAXE;
	}

	public function getDrops(Item $item){
		if($item->isPickaxe() >= Tool::TIER_WOODEN){
			return [
				[Item::MOSSY_COBBLESTONE, $this->meta, 1],
			];
		}else{
			return [];
		}
	}
}