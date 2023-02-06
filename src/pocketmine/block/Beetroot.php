<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\Item;

use function mt_rand;

class Beetroot extends Crops{

	protected $id = self::BEETROOT_BLOCK;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getName(){
		return "Beetroot Block";
	}

	public function getDrops(Item $item){
		$drops = [];
		if($this->meta >= 0x07){
			$drops[] = [Item::BEETROOT, 0, 1];
			$drops[] = [Item::BEETROOT_SEEDS, 0, mt_rand(0, 3)];
		}else{
			$drops[] = [Item::BEETROOT_SEEDS, 0, 1];
		}

		return $drops;
	}

	public function getPickedItem() : Item{
		return Item::get(Item::BEETROOT_SEEDS);
	}
}