<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\item\Shears;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Player;

use function mt_rand;

class DoublePlant extends Flowable{
	public const BITFLAG_TOP = 0x08;

	protected $id = self::DOUBLE_PLANT;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function canBeReplaced(){
		return $this->meta === 2 or $this->meta === 3; //grass or fern
	}

	public function getName(){
		static $names = [
			0 => "Sunflower",
			1 => "Lilac",
			2 => "Double Tallgrass",
			3 => "Large Fern",
			4 => "Rose Bush",
			5 => "Peony"
		];
		return $names[$this->meta & 0x07] ?? "";
	}

	public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null){
		$id = $block->getSide(Vector3::SIDE_DOWN)->getId();
		if(($id === Block::GRASS or $id === Block::DIRT) and $block->getSide(Vector3::SIDE_UP)->canBeReplaced()){
			$this->getLevel()->setBlock($block, $this, false, false);
			$this->getLevel()->setBlock($block->getSide(Vector3::SIDE_UP), Block::get($this->id, $this->meta | self::BITFLAG_TOP), false, false);

			return true;
		}

		return false;
	}

	/**
	 * Returns whether this double-plant has a corresponding other half.
	 * @return bool
	 */
	public function isValidHalfPlant() : bool{
		if($this->meta & self::BITFLAG_TOP){
			$other = $this->getSide(Vector3::SIDE_DOWN);
		}else{
			$other = $this->getSide(Vector3::SIDE_UP);
		}

		return (
			$other->getId() === $this->getId() and
			($other->getDamage() & 0x07) === ($this->getDamage() & 0x07) and
			($other->getDamage() & self::BITFLAG_TOP) !== ($this->getDamage() & self::BITFLAG_TOP)
		);
	}

	public function onUpdate($type){
		if($type === Level::BLOCK_UPDATE_NORMAL){
			$down = $this->getSide(Vector3::SIDE_DOWN);
			if(!$this->isValidHalfPlant() or (($this->meta & self::BITFLAG_TOP) === 0 and $down->isTransparent())){
				$this->getLevel()->useBreakOn($this);

				return Level::BLOCK_UPDATE_NORMAL;
			}
		}

		return false;
	}

	public function onBreak(Item $item){
		if(parent::onBreak($item) and $this->isValidHalfPlant()){
			return $this->getLevel()->setBlock($this->getSide(($this->meta & self::BITFLAG_TOP) !== 0 ? Vector3::SIDE_DOWN : Vector3::SIDE_UP), Block::get(Block::AIR));
		}

		return false;
	}

	public function getDrops(Item $item){
		if(!$item instanceof Shears and ($this->meta === 2 or $this->meta === 3)){ //grass or fern
			if(mt_rand(0, 24) === 0){
				return [
					[Item::SEEDS, 0, 1]
				];
			}else{
				return [];
			}
		}

		return [
			[$this->id, $this->meta & 0x07, 1]
		];
	}

	public function getAffectedBlocks() : array{
		if($this->isValidHalfPlant()){
			return [$this, $this->getSide(($this->meta & self::BITFLAG_TOP) !== 0 ? Vector3::SIDE_DOWN : Vector3::SIDE_UP)];
		}

		return parent::getAffectedBlocks();
	}
}