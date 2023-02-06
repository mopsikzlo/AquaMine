<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\Item;

use function mt_rand;

class Wheat extends Crops{

	protected $id = self::WHEAT_BLOCK;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getName(){
		return "Wheat Block";
	}

	public function getDrops(Item $item){
		$drops = [];
		if($this->meta >= 0x07){
			$drops[] = [Item::WHEAT, 0, 1];
			$drops[] = [Item::WHEAT_SEEDS, 0, mt_rand(0, 3)];
		}else{
			$drops[] = [Item::WHEAT_SEEDS, 0, 1];
		}

		return $drops;
	}

	public function getPickedItem() : Item{
		return Item::get(Item::WHEAT);
	}
}