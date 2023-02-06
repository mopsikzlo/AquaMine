<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\item\Shears;
use pocketmine\item\Tool;
use pocketmine\level\Level;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use pocketmine\Player;

use function max;
use function min;

class Vine extends Transparent{
	public const FLAG_SOUTH = 0x01;
	public const FLAG_WEST = 0x02;
	public const FLAG_NORTH = 0x04;
	public const FLAG_EAST = 0x08;

	protected $id = self::VINE;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function isSolid(){
		return false;
	}

	public function getName(){
		return "Vines";
	}

	public function getHardness(){
		return 0.2;
	}

	public function canPassThrough(){
		return true;
	}

	public function hasEntityCollision(){
		return true;
	}

	public function canClimb() : bool{
		return true;
	}

	public function onEntityCollide(Entity $entity){
		$entity->resetFallDistance();
	}

	protected function recalculateBoundingBox(){

		$f1 = 1;
		$f2 = 1;
		$f3 = 1;
		$f4 = 0;
		$f5 = 0;
		$f6 = 0;

		$flag = $this->meta > 0;

		if(($this->meta & self::FLAG_WEST) > 0){
			$f4 = max($f4, 0.0625);
			$f1 = 0;
			$f2 = 0;
			$f5 = 1;
			$f3 = 0;
			$f6 = 1;
			$flag = true;
		}

		if(($this->meta & self::FLAG_EAST) > 0){
			$f1 = min($f1, 0.9375);
			$f4 = 1;
			$f2 = 0;
			$f5 = 1;
			$f3 = 0;
			$f6 = 1;
			$flag = true;
		}

		if(($this->meta & self::FLAG_SOUTH) > 0){
			$f3 = min($f3, 0.9375);
			$f6 = 1;
			$f1 = 0;
			$f4 = 1;
			$f2 = 0;
			$f5 = 1;
			$flag = true;
		}

		if(!$flag and $this->getSide(Vector3::SIDE_UP)->isSolid()){
			$f2 = min($f2, 0.9375);
			$f5 = 1;
			$f1 = 0;
			$f4 = 1;
			$f3 = 0;
			$f6 = 1;
		}

		return new AxisAlignedBB(
			$this->x + $f1,
			$this->y + $f2,
			$this->z + $f3,
			$this->x + $f4,
			$this->y + $f5,
			$this->z + $f6
		);
	}


	public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null){
		//TODO: multiple sides
		if($target->isSolid()){
			$faces = [
				2 => self::FLAG_SOUTH,
				3 => self::FLAG_NORTH,
				4 => self::FLAG_EAST,
				5 => self::FLAG_WEST,
			];
			if(isset($faces[$face])){
				$this->meta = $faces[$face];
				$this->getLevel()->setBlock($block, $this, true, true);

				return true;
			}
		}

		return false;
	}

	public function onUpdate($type){
		if($type === Level::BLOCK_UPDATE_NORMAL){
			$sides = [
				1 => 3,
				2 => 4,
				4 => 2,
				8 => 5
			];

			if(!isset($sides[$this->meta])){
				return false; //TODO: remove this once placing on multiple sides is supported (these are bitflags, not actual meta values
			}

			if(!$this->getSide($sides[$this->meta])->isSolid()){ //Replace with common break method
				$this->level->useBreakOn($this);
				return Level::BLOCK_UPDATE_NORMAL;
			}
		}

		return false;
	}

	public function getDrops(Item $item){
		if($item instanceof Shears){
			return [
				[$this->id, 0, 1],
			];
		}else{
			return [];
		}
	}

	public function getToolType(){
		return Tool::TYPE_AXE;
	}
}