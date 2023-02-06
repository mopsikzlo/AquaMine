<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\event\block\BlockSpreadEvent;
use pocketmine\item\Item;
use pocketmine\item\Shovel;
use pocketmine\item\Tool;
use pocketmine\level\generator\object\TallGrass as TallGrassObject;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\utils\Random;

use function mt_rand;

class Grass extends Solid{

	protected $id = self::GRASS;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getName(){
		return "Grass";
	}

	public function getHardness(){
		return 0.6;
	}

	public function getToolType(){
		return Tool::TYPE_SHOVEL;
	}

	public function getDrops(Item $item){
		return [
			[Item::DIRT, 0, 1],
		];
	}

	public function onUpdate($type){
		if($type === Level::BLOCK_UPDATE_RANDOM){
			$lightAbove = $this->level->getFullLightAt($this->x, $this->y + 1, $this->z);
			if($lightAbove < 4 and Block::$lightFilter[$this->level->getBlockIdAt($this->x, $this->y + 1, $this->z)] >= 3){ //2 plus 1 standard filter amount
				//grass dies
				$ev = new BlockSpreadEvent($this, $this, Block::get(Block::DIRT));
				$ev->call();
				if(!$ev->isCancelled()){
					$this->level->setBlock($this, $ev->getNewState(), false, false);
				}

				return Level::BLOCK_UPDATE_RANDOM;
			}elseif($lightAbove >= 9){
				//try grass spread
				for($i = 0; $i < 4; ++$i){
					$x = mt_rand($this->x - 1, $this->x + 1);
					$y = mt_rand($this->y - 3, $this->y + 1);
					$z = mt_rand($this->z - 1, $this->z + 1);
					if(
						$this->level->getBlockIdAt($x, $y, $z) !== Block::DIRT or
						$this->level->getBlockDataAt($x, $y, $z) === 1 or
						$this->level->getFullLightAt($x, $y + 1, $z) < 4 or
						Block::$lightFilter[$this->level->getBlockIdAt($x, $y + 1, $z)] >= 3
					){
						continue;
					}
					$ev = new BlockSpreadEvent($b = $this->level->getBlockAt($x, $y, $z), $this, Block::get(Block::GRASS));
					$ev->call();
					if(!$ev->isCancelled()){
						$this->level->setBlock($b, $ev->getNewState(), false, false);
					}
				}
				return Level::BLOCK_UPDATE_RANDOM;
			}
		}

		return false;
	}

	public function onActivate(Item $item, Player $player = null){
		if($item->getId() === Item::DYE and $item->getDamage() === 0x0F){
			$item->count--;
			TallGrassObject::growGrass($this->getLevel(), $this, new Random(mt_rand()), 8, 2);

			return true;
		}elseif($item instanceof Hoe){
            $item->applyDamage(1);
			$this->getLevel()->setBlock($this, new Farmland());

			return true;
		}elseif($item instanceof Shovel and $this->getSide(Vector3::SIDE_UP)->getId() === Block::AIR){
            $item->applyDamage(1);
            $this->getLevel()->setBlock($this, new GrassPath());

			return true;
		}

		return false;
	}

	public function canBeActivated() : bool{
		return true;
	}
}
