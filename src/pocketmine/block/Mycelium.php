<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\event\block\BlockSpreadEvent;
use pocketmine\item\Item;
use pocketmine\item\Tool;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Server;

use function mt_rand;

class Mycelium extends Solid{

	protected $id = self::MYCELIUM;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getName(){
		return "Mycelium";
	}

	public function getToolType(){
		return Tool::TYPE_SHOVEL;
	}

	public function getHardness(){
		return 0.6;
	}

	public function getDrops(Item $item){
		return [
			[Item::DIRT, 0, 1],
		];
	}

	public function onUpdate($type){
		if($type === Level::BLOCK_UPDATE_RANDOM){
			//TODO: light levels
			$x = mt_rand($this->x - 1, $this->x + 1);
			$y = mt_rand($this->y - 2, $this->y + 2);
			$z = mt_rand($this->z - 1, $this->z + 1);
			$block = $this->getLevel()->getBlockAt($x, $y, $z);
			if($block->getId() === Block::DIRT){
				if($block->getSide(Vector3::SIDE_UP) instanceof Transparent){
					$ev = new BlockSpreadEvent($block, $this, new Mycelium());
					$ev->call();
					if(!$ev->isCancelled()){
						$this->getLevel()->setBlock($block, $ev->getNewState());
					}
				}
			}
		}
	}
}
