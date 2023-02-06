<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\event\block\BlockGrowEvent;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Player;

use function mt_rand;

abstract class Crops extends Flowable{

	public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null){
		if($block->getSide(Vector3::SIDE_DOWN)->getId() === Block::FARMLAND){
			$this->getLevel()->setBlock($block, $this, true, true);

			return true;
		}

		return false;
	}


	public function onActivate(Item $item, Player $player = null){
		if($item->getId() === Item::DYE and $item->getDamage() === 0x0F){ //Bonemeal
			$block = clone $this;
			$block->meta += mt_rand(2, 5);
			if($block->meta > 7){
				$block->meta = 7;
			}

			$ev = new BlockGrowEvent($this, $block);
			$ev->call();

			if(!$ev->isCancelled()){
				$this->getLevel()->setBlock($this, $ev->getNewState(), true, true);
			}

			$item->count--;

			return true;
		}

		return false;
	}

	public function canBeActivated() : bool{
		return true;
	}

	public function onUpdate($type){
		if($type === Level::BLOCK_UPDATE_NORMAL){
			if($this->getSide(Vector3::SIDE_DOWN)->getId() !== Block::FARMLAND){
				$this->getLevel()->useBreakOn($this);
				return Level::BLOCK_UPDATE_NORMAL;
			}
		}elseif($type === Level::BLOCK_UPDATE_RANDOM){
			if(mt_rand(0, 2) === 1){
				if($this->meta < 0x07){
					$block = clone $this;
					++$block->meta;
					$ev = new BlockGrowEvent($this, $block);
					$ev->call();

					if(!$ev->isCancelled()){
						$this->getLevel()->setBlock($this, $ev->getNewState(), true, true);
					}else{
						return Level::BLOCK_UPDATE_RANDOM;
					}
				}
			}else{
				return Level::BLOCK_UPDATE_RANDOM;
			}
		}

		return false;
	}
}