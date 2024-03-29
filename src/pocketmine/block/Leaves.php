<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\event\block\LeavesDecayEvent;
use pocketmine\item\Item;
use pocketmine\item\Shears;
use pocketmine\item\Tool;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Player;

use function mt_rand;

class Leaves extends Transparent{
	public const OAK = 0;
	public const SPRUCE = 1;
	public const BIRCH = 2;
	public const JUNGLE = 3;
	public const ACACIA = 0;
	public const DARK_OAK = 1;

	protected $id = self::LEAVES;
	protected $woodType = self::WOOD;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getHardness(){
		return 0.2;
	}

	public function getToolType(){
		return Tool::TYPE_SHEARS;
	}

	public function getName(){
		static $names = [
			self::OAK => "Oak Leaves",
			self::SPRUCE => "Spruce Leaves",
			self::BIRCH => "Birch Leaves",
			self::JUNGLE => "Jungle Leaves",
		];
		return $names[$this->meta & 0x03];
	}

	public function diffusesSkyLight() : bool{
		return true;
	}

	protected function findLog(Block $pos, array $visited, $distance, &$check, $fromSide = null){
		++$check;
		$index = $pos->x . "." . $pos->y . "." . $pos->z;
		if(isset($visited[$index])){
			return false;
		}
		if($pos->getId() === $this->woodType){
			return true;
		}elseif($pos->getId() === $this->id and $distance < 3){
			$visited[$index] = true;
			$down = $pos->getSide(Vector3::SIDE_DOWN)->getId();
			if($down === $this->woodType){
				return true;
			}
			if($fromSide === null){
				for($side = 2; $side <= 5; ++$side){
					if($this->findLog($pos->getSide($side), $visited, $distance + 1, $check, $side) === true){
						return true;
					}
				}
			}else{ //No more loops
				switch($fromSide){
					case 2:
						if($this->findLog($pos->getSide(Vector3::SIDE_NORTH), $visited, $distance + 1, $check, $fromSide) === true){
							return true;
						}elseif($this->findLog($pos->getSide(Vector3::SIDE_WEST), $visited, $distance + 1, $check, $fromSide) === true){
							return true;
						}elseif($this->findLog($pos->getSide(Vector3::SIDE_EAST), $visited, $distance + 1, $check, $fromSide) === true){
							return true;
						}
						break;
					case 3:
						if($this->findLog($pos->getSide(Vector3::SIDE_SOUTH), $visited, $distance + 1, $check, $fromSide) === true){
							return true;
						}elseif($this->findLog($pos->getSide(Vector3::SIDE_WEST), $visited, $distance + 1, $check, $fromSide) === true){
							return true;
						}elseif($this->findLog($pos->getSide(Vector3::SIDE_EAST), $visited, $distance + 1, $check, $fromSide) === true){
							return true;
						}
						break;
					case 4:
						if($this->findLog($pos->getSide(Vector3::SIDE_NORTH), $visited, $distance + 1, $check, $fromSide) === true){
							return true;
						}elseif($this->findLog($pos->getSide(Vector3::SIDE_SOUTH), $visited, $distance + 1, $check, $fromSide) === true){
							return true;
						}elseif($this->findLog($pos->getSide(Vector3::SIDE_WEST), $visited, $distance + 1, $check, $fromSide) === true){
							return true;
						}
						break;
					case 5:
						if($this->findLog($pos->getSide(Vector3::SIDE_NORTH), $visited, $distance + 1, $check, $fromSide) === true){
							return true;
						}elseif($this->findLog($pos->getSide(Vector3::SIDE_SOUTH), $visited, $distance + 1, $check, $fromSide) === true){
							return true;
						}elseif($this->findLog($pos->getSide(Vector3::SIDE_EAST), $visited, $distance + 1, $check, $fromSide) === true){
							return true;
						}
						break;
				}
			}
		}

		return false;
	}

	public function onUpdate($type){
		if($type === Level::BLOCK_UPDATE_NORMAL){
			if(($this->meta & 0b00001100) === 0){
				$this->meta |= 0x08;
				$this->getLevel()->setBlock($this, $this, true, false);
			}
		}elseif($type === Level::BLOCK_UPDATE_RANDOM){
			if(($this->meta & 0b00001100) === 0x08){
				$this->meta &= 0x03;
				$visited = [];
				$check = 0;

				$ev = new LeavesDecayEvent($this);
				$ev->call();

				if($ev->isCancelled() or $this->findLog($this, $visited, 0, $check) === true){
					$this->getLevel()->setBlock($this, $this, false, false);
				}else{
					$this->getLevel()->useBreakOn($this);

					return Level::BLOCK_UPDATE_NORMAL;
				}
			}
		}

		return false;
	}

	public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null){
		$this->meta |= 0x04;
		$this->getLevel()->setBlock($this, $this, true);
	}

	public function getDrops(Item $item){
		$drops = [];
		if($item instanceof Shears){
			$drops[] = [$this->id, $this->meta & 0x03, 1];
		}else{
			if(mt_rand(1, 20) === 1){ //Saplings
				$drops[] = [Item::SAPLING, $this->meta & 0x03, 1];
			}
			if(($this->meta & 0x03) === self::OAK and mt_rand(1, 200) === 1){ //Apples
				$drops[] = [Item::APPLE, 0, 1];
			}
		}

		return $drops;
	}
}