<?php

declare(strict_types=1);

namespace pocketmine\block;


use pocketmine\event\block\BlockGrowEvent;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Player;

use function mt_rand;

class NetherWartPlant extends Flowable{
	protected $id = Block::NETHER_WART_PLANT;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null){
		$down = $this->getSide(Vector3::SIDE_DOWN);
		if($down->getId() === Block::SOUL_SAND){
			$this->getLevel()->setBlock($block, $this, false, true);

			return true;
		}

		return false;
	}

	public function onUpdate($type){
		switch($type){
			case Level::BLOCK_UPDATE_RANDOM:
				if($this->meta < 3 and mt_rand(0, 10) === 0){ //Still growing
					$block = clone $this;
					$block->meta++;
					$ev = new BlockGrowEvent($this, $block);
					$ev->call();

					if(!$ev->isCancelled()){
						$this->getLevel()->setBlock($this, $ev->getNewState(), false, true);

						return $type;
					}
				}
				break;
			case Level::BLOCK_UPDATE_NORMAL:
				if($this->getSide(Vector3::SIDE_DOWN)->getId() !== Block::SOUL_SAND){
					$this->getLevel()->useBreakOn($this);
					return $type;
				}
				break;
		}

		return false;
	}

	public function getDrops(Item $item){
		return [[Item::NETHER_WART, 0, ($this->meta === 3 ? mt_rand(2, 4) : 1)]];
	}
}