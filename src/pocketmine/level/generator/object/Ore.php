<?php

declare(strict_types = 1);

namespace pocketmine\level\generator\object;

use pocketmine\level\loadchunk\ChunkManager;
use pocketmine\math\VectorMath;
use pocketmine\utils\Random;

class Ore {
	private $random;
	public $type;

	/**
	 * Ore constructor.
	 *
	 * @param Random  $random
	 * @param OreType $type
	 */
	public function __construct(Random $random, OreType $type){
		$this->type = $type;
		$this->random = $random;
	}

	/**
	 * @return OreType
	 */
	public function getType(){
		return $this->type;
	}

	/**
	 * @param ChunkManager $level
	 * @param              $x
	 * @param              $y
	 * @param              $z
	 *
	 * @return bool
	 */
	public function canPlaceObject(ChunkManager $level, $x, $y, $z){
		return (($level->getBlockIdAt($x, $y, $z) === 1) or ($level->getBlockIdAt($x, $y, $z) === 87));
	}

	/**
	 * @param ChunkManager $level
	 * @param              $x
	 * @param              $y
	 * @param              $z
	 */
	public function placeObject(ChunkManager $level, $x, $y, $z){
		$clusterSize = (int) $this->type->clusterSize;
		$angle = $this->random->nextFloat() * M_PI;
		$offset = VectorMath::getDirection2D($angle)->multiply($clusterSize / 8);
		$x1 = $x + 8 + $offset->x;
		$x2 = $x + 8 - $offset->x;
		$z1 = $z + 8 + $offset->y;
		$z2 = $z + 8 - $offset->y;
		$y1 = $y + $this->random->nextBoundedInt(3) + 2;
		$y2 = $y + $this->random->nextBoundedInt(3) + 2;
		for($count = 0; $count <= $clusterSize; ++$count){
			$seedX = $x1 + ($x2 - $x1) * $count / $clusterSize;
			$seedY = $y1 + ($y2 - $y1) * $count / $clusterSize;
			$seedZ = $z1 + ($z2 - $z1) * $count / $clusterSize;
			$size = ((sin($count * (M_PI / $clusterSize)) + 1) * $this->random->nextFloat() * $clusterSize / 16 + 1) / 2;

			$startX = (int) ($seedX - $size);
			$startY = (int) ($seedY - $size);
			$startZ = (int) ($seedZ - $size);
			$endX = (int) ($seedX + $size);
			$endY = (int) ($seedY + $size);
			$endZ = (int) ($seedZ + $size);

			for($x = $startX; $x <= $endX; ++$x){
				$sizeX = ($x + 0.5 - $seedX) / $size;
				$sizeX *= $sizeX;

				if($sizeX < 1){
					for($y = $startY; $y <= $endY; ++$y){
						$sizeY = ($y + 0.5 - $seedY) / $size;
						$sizeY *= $sizeY;

						if($y > 0 and ($sizeX + $sizeY) < 1){
							for($z = $startZ; $z <= $endZ; ++$z){
								$sizeZ = ($z + 0.5 - $seedZ) / $size;
								$sizeZ *= $sizeZ;

								if(($sizeX + $sizeY + $sizeZ) < 1 and (($level->getBlockIdAt($x, $y, $z) === 1) or ($level->getBlockIdAt($x, $y, $z) === 87))){
									$level->setBlockIdAt($x, $y, $z, $this->type->material->getId());
									if($this->type->material->getDamage() !== 0){
										$level->setBlockDataAt($x, $y, $z, $this->type->material->getDamage());
									}
								}
							}
						}
					}
				}
			}
		}
	}

}