<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\item\Tool;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use pocketmine\Player;

abstract class Stair extends Transparent{

	private const Y_ROT = [
		0 => 2, //EAST -> SOUTH
		2 => 1, //SOUTH -> WEST
		1 => 3, //WEST -> NORTH
		3 => 0 //NORTH -> EAST
	];

	protected function recalculateCollisionBoxes() : array{
		$minYSlab = ($this->meta & 0x04) === 0 ? 0 : 0.5;
		$maxYSlab = $minYSlab + 0.5;

		$bbs = [
			new AxisAlignedBB(
				$this->x,
				$this->y + $minYSlab,
				$this->z,
				$this->x + 1,
				$this->y + $maxYSlab,
				$this->z + 1
			)
		];

		$minY = ($this->meta & 0x04) === 0 ? 0.5 : 0;

		$rotationMeta = $this->meta & 0x03;

		$topStep = new AxisAlignedBB($this->x, $this->y + $minY, $this->z, $this->x + 1, $this->y + $minY + 0.5, $this->z + 1);
		self::setBoundsForRotation($topStep, $this, $rotationMeta);

		if(($backRotation = $this->getPossibleCornerRotation(false)) !== null){
			self::setBoundsForRotation($topStep, $this, $backRotation);
		}elseif(($frontRotation = $this->getPossibleCornerRotation(true)) !== null){
			//add an extra cube
			$extraCube = new AxisAlignedBB($this->x, $this->y + $minY, $this->z, $this->x + 1, $this->y + $minY + 0.5, $this->z + 1);
			self::setBoundsForRotation($extraCube, $this, Vector3::getOppositeSide($rotationMeta));
			self::setBoundsForRotation($extraCube, $this, $frontRotation);
			$bbs[] = $extraCube;
		}

		$bbs[] = $topStep;
		return $bbs;
	}

	private function getPossibleCornerRotation(bool $oppositeRotation) : ?int{
		$rotationMeta = $this->meta & 0x03;
		$side = $this->getSide($oppositeRotation ? Vector3::getOppositeSide($rotationMeta) : $rotationMeta);
		if($side instanceof Stair and ($side->getDamage() & 0x04) === ($this->meta & 0x04) and (
			($sideRotation = $side->getDamage() & 0x03) === self::Y_ROT[$rotationMeta] or
			$sideRotation === Vector3::getOppositeSide(self::Y_ROT[$rotationMeta]))
		){
			return $sideRotation;
		}
		return null;
	}

	private static function setBoundsForRotation(AxisAlignedBB $bb, Vector3 $base, int $rotationMeta) : void{
		switch($rotationMeta){
			case 0: //EAST
				$bb->minX = $base->x + 0.5;
				break;
			case 1: //WEST
				$bb->maxX = $base->x + 0.5;
				break;
			case 2: //SOUTH
				$bb->minZ = $base->z + 0.5;
				break;
			case 3: //NORTH
				$bb->maxZ = $base->z + 0.5;
				break;
		}
	}

	public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null){
		$faces = [
			0 => 0,
			1 => 2,
			2 => 1,
			3 => 3,
		];
		$this->meta = $faces[$player->getDirection()] & 0x03;
		if(($fy > 0.5 and $face !== 1) or $face === 0){
			$this->meta |= 0x04; //Upside-down stairs
		}
		$this->getLevel()->setBlock($block, $this, true, true);

		return true;
	}

	public function getDrops(Item $item){
		if($item->isPickaxe() >= Tool::TIER_WOODEN){
			return [
				[$this->getId(), 0, 1],
			];
		}else{
			return [];
		}
	}
}
