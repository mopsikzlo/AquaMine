<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\Item;

class SeaLantern extends Transparent{

	protected $id = self::SEA_LANTERN;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getName(){
		return "Sea Lantern";
	}

	public function getHardness(){
		return 0.3;
	}

	public function getLightLevel(){
		return 15;
	}

	public function getDrops(Item $item){
		return [
			[Item::PRISMARINE_CRYSTALS, 0, 3],
		];
	}

}
