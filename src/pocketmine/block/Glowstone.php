<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\item\Tool;

use function mt_rand;

class Glowstone extends Transparent{

	protected $id = self::GLOWSTONE;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getName(){
		return "Glowstone";
	}

	public function getHardness(){
		return 0.3;
	}

	public function getToolType(){
		return Tool::TYPE_PICKAXE;
	}

	public function getLightLevel(){
		return 15;
	}

	public function getDrops(Item $item){
		return [
			[Item::GLOWSTONE_DUST, 0, mt_rand(2, 4)],
		];
	}
}