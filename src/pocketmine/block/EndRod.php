<?php

namespace pocketmine\block;


use pocketmine\item\Item;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use pocketmine\Player;

class EndRod extends Flowable{

	protected $id = self::END_ROD;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getName(){
		return "End Rod";
	}

	public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null){
		if($face === Vector3::SIDE_UP or $face === Vector3::SIDE_DOWN){
			$this->meta = $face;
		}else{
			$this->meta = $face ^ 0x01;
		}
		if($target instanceof EndRod and $target->getDamage() === $this->meta){
			$this->meta ^= 0x01;
		}

		return $this->level->setBlock($block, $this, true, true);
	}

	public function isSolid(){
		return true;
	}

	public function getLightLevel(){
		return 14;
	}

	protected function recalculateBoundingBox(){
		$m = $this->meta & ~0x01;
		$width = 0.375;

		switch($m){
			case 0x00: //up/down
				return new AxisAlignedBB(
					$this->x + $width,
					$this->y,
					$this->z + $width,
					$this->x + 1 - $width,
					$this->y + 1,
					$this->z + 1 - $width
				);
			case 0x02: //north/south
				return new AxisAlignedBB(
					$this->x,
					$this->y + $width,
					$this->z + $width,
					$this->x + 1,
					$this->y + 1 - $width,
					$this->z + 1 - $width
				);
			case 0x04: //east/west
				return new AxisAlignedBB(
					$this->x + $width,
					$this->y + $width,
					$this->z,
					$this->x + 1 - $width,
					$this->y + 1 - $width,
					$this->z + 1
				);
		}

		return null;
	}

	public function getDrops(Item $item){
		return [
			[$this->getId(), 0, 1]
		];
	}

}