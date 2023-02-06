<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\item\Tool;
use pocketmine\math\AxisAlignedBB;

class Farmland extends Transparent{

	protected $id = self::FARMLAND;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getName(){
		return "Farmland";
	}

	public function getHardness(){
		return 0.6;
	}

	public function getToolType(){
		return Tool::TYPE_SHOVEL;
	}

	protected function recalculateBoundingBox(){
		return new AxisAlignedBB(
			$this->x,
			$this->y,
			$this->z,
			$this->x + 1,
			$this->y + 1, //TODO: this should be 0.9375, but MCPE currently treats them as a full block (https://bugs.mojang.com/browse/MCPE-12109)
			$this->z + 1
		);
	}

	public function getDrops(Item $item){
		return [
			[Item::DIRT, 0, 1],
		];
	}

	public function getPickedItem() : Item{
		return Item::get(Item::DIRT);
	}
}