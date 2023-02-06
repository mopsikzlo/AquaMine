<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Player;

class RedMushroom extends Flowable{

	protected $id = self::RED_MUSHROOM;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getName(){
		return "Red Mushroom";
	}


	public function onUpdate($type){
		if($type === Level::BLOCK_UPDATE_NORMAL){
			if($this->getSide(Vector3::SIDE_DOWN)->isTransparent() === true){
				$this->getLevel()->useBreakOn($this);

				return Level::BLOCK_UPDATE_NORMAL;
			}
		}

		return false;
	}

	public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null){
		$down = $this->getSide(Vector3::SIDE_DOWN);
		if($down->isTransparent() === false){
			$this->getLevel()->setBlock($block, $this, true, true);

			return true;
		}

		return false;
	}
}