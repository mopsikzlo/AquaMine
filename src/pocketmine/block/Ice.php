<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\item\Tool;
use pocketmine\level\Level;

class Ice extends Transparent{

	protected $id = self::ICE;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getName(){
		return "Ice";
	}

	public function getHardness(){
		return 0.5;
	}

	public function getLightFilter() : int{
		return 2;
	}

	public function getFrictionFactor(){
		return 0.98;
	}

	public function getToolType(){
		return Tool::TYPE_PICKAXE;
	}

	public function onBreak(Item $item){
		$this->getLevel()->setBlock($this, new Water(), true);

		return true;
	}

	public function onUpdate($type){
		if($type === Level::BLOCK_UPDATE_RANDOM){
			if($this->level->getHighestAdjacentBlockLight($this->x, $this->y, $this->z) >= 12){
				$this->level->useBreakOn($this);

				return $type;
			}
		}
		return false;
	}

	public function getDrops(Item $item){
		return [];
	}
}