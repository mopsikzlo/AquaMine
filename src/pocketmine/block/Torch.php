<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Player;

class Torch extends Flowable{

	protected $id = self::TORCH;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getLightLevel(){
		return 14;
	}

	public function getName(){
		return "Torch";
	}


	public function onUpdate($type){
		if($type === Level::BLOCK_UPDATE_NORMAL){
			$below = $this->getSide(Vector3::SIDE_DOWN);
			$side = $this->getDamage();
			$faces = [
				0 => 0,
				1 => 4,
				2 => 5,
				3 => 2,
				4 => 3,
				5 => 0
			];

			if($this->getSide($faces[$side])->isTransparent() === true and !($side === 0 and ($below->getId() === self::FENCE or $below->getId() === self::COBBLESTONE_WALL))){
				$this->getLevel()->useBreakOn($this);

				return Level::BLOCK_UPDATE_NORMAL;
			}
		}

		return false;
	}

	public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null){
		$below = $this->getSide(Vector3::SIDE_DOWN);

		if($target->isTransparent() === false and $face !== 0){
			$faces = [
				1 => 5,
				2 => 4,
				3 => 3,
				4 => 2,
				5 => 1,
			];
			$this->meta = $faces[$face];
			$this->getLevel()->setBlock($block, $this, true, true);

			return true;
		}elseif($below->isTransparent() === false or $below->getId() === self::FENCE or $below->getId() === self::COBBLESTONE_WALL){
			$this->meta = 0;
			$this->getLevel()->setBlock($block, $this, true, true);

			return true;
		}

		return false;
	}

	public function getDrops(Item $item){
		return [
			[$this->id, 0, 1],
		];
	}
}