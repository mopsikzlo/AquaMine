<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\item\Tool;

class Netherrack extends Solid{

	protected $id = self::NETHERRACK;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getName(){
		return "Netherrack";
	}

	public function getHardness(){
		return 0.4;
	}

	public function getToolType(){
		return Tool::TYPE_PICKAXE;
	}

	public function getDrops(Item $item){
		if($item->isPickaxe() >= Tool::TIER_WOODEN){
			return [
				[Item::NETHERRACK, 0, 1],
			];
		}else{
			return [];
		}
	}
}