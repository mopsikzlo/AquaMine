<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\item\Tool;

use function mt_rand;

class Gravel extends Fallable{

	protected $id = self::GRAVEL;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getName(){
		return "Gravel";
	}

	public function getHardness(){
		return 0.6;
	}

	public function getToolType(){
		return Tool::TYPE_SHOVEL;
	}

	public function getDrops(Item $item){
		if(mt_rand(1, 10) === 1){
			return [
				[Item::FLINT, 0, 1],
			];
		}

		return [
			[Item::GRAVEL, 0, 1],
		];
	}

}