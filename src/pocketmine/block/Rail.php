<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Player;

class Rail extends Flowable{

	public const STRAIGHT_NORTH_SOUTH = 0;
	public const STRAIGHT_EAST_WEST = 1;
	public const ASCENDING_EAST = 2;
	public const ASCENDING_WEST = 3;
	public const ASCENDING_NORTH = 4;
	public const ASCENDING_SOUTH = 5;
	public const CURVE_SOUTHEAST = 6;
	public const CURVE_SOUTHWEST = 7;
	public const CURVE_NORTHWEST = 8;
	public const CURVE_NORTHEAST = 9;

	protected $id = self::RAIL;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getName(){
		return "Rail";
	}

	public function getHardness(){
		return 0.7;
	}

	public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null){
		if(!$block->getSide(Vector3::SIDE_DOWN)->isTransparent()){
			return $this->getLevel()->setBlock($block, $this, true, true);
		}

		return false;
	}

	public function onUpdate($type){
		if($type === Level::BLOCK_UPDATE_NORMAL){
			if($this->getSide(Vector3::SIDE_DOWN)->isTransparent()){
				$this->getLevel()->useBreakOn($this);
				return $type;
			}else{
				//TODO: Update rail connectivity
			}
		}

		return false;
	}
}
