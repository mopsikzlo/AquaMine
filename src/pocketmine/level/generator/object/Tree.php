<?php

namespace pocketmine\level\generator\object;

use pocketmine\block\Block;
use pocketmine\block\Sapling;
use pocketmine\level\loadchunk\ChunkManager;
use pocketmine\utils\Random;

abstract class Tree{
	public $overridable = [
		Block::AIR => \true,
		Block::SAPLING => \true,
		Block::LOG => \true,
		Block::LEAVES => \true,
		Block::SNOW_LAYER => \true,
		Block::LOG2 => \true,
		Block::LEAVES2 => \true
	];

	public $type = 0;
	public $trunkBlock = Block::LOG;
	public $leafBlock = Block::LEAVES;
	public $treeHeight = 15;

	public static function growTree(ChunkManager $level, $x, $y, $z, Random $random, $type = 0){
		switch($type){
			case Sapling::SPRUCE:
				$tree = new SpruceTree();
				break;
			case Sapling::BIRCH:
					$tree = new BirchTree();				
				break;
			case Sapling::JUNGLE:
				$tree = new JungleTree();
				break;
			case Sapling::ACACIA:
				$tree = new AcaciaTree();
				break;
			case Sapling::DARK_OAK:
				$tree = new DarkOakTree();
				break;
			case Sapling::OAK:
			default:
					$tree = new OakTree();				
				break;
		}
		if($tree->canPlaceObject($level, $x, $y, $z, $random)){
			$tree->placeObject($level, $x, $y, $z, $random);
		}
	}

	public function canPlaceObject(ChunkManager $level, $x, $y, $z, Random $random) {
		$radiusToCheck = 0;
		for($yy = 0; $yy < $this->treeHeight + 3; ++$yy){
			if($yy === 1 or $yy === $this->treeHeight){
				++$radiusToCheck;
			}
			for($xx = -$radiusToCheck; $xx < ($radiusToCheck + 1); ++$xx){
				for($zz = -$radiusToCheck; $zz < ($radiusToCheck + 1); ++$zz){
					if(!isset($this->overridable[$level->getBlockIdAt($x + $xx, $y + $yy, $z + $zz)])){
						return \false;
					}
				}
			}
		}
		return \true;
	}

	public function placeObject(ChunkManager $level, $x, $y, $z, Random $random){
		for($yy = $y - 3 + $this->treeHeight; $yy <= $y + $this->treeHeight; ++$yy){
			$yOff = $yy - ($y + $this->treeHeight);
			$mid = (int) (1 - $yOff / 2);
			for($xx = $x - $mid; $xx <= $x + $mid; ++$xx){
				$xOff = \abs($xx - $x);
				for($zz = $z - $mid; $zz <= $z + $mid; ++$zz){
					$zOff = \abs($zz - $z);
					if($xOff === $mid and $zOff === $mid and ($yOff === 0 or $random->nextBoundedInt(2) === 0)){
						continue;
					}
					if(!Block::$solid[$level->getBlockIdAt($xx, $yy, $zz)]){
						$level->setBlockIdAt($xx, $yy, $zz, $this->leafBlock);
						$level->setBlockDataAt($xx, $yy, $zz, $this->type);
					}
				}
			}
		}
	}

}

?>