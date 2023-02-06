<?php

namespace pocketmine\level\generator\populator;

use pocketmine\block\Water;
use pocketmine\level\loadchunk\ChunkManager;
use pocketmine\utils\Random;

class Pond extends Populator {
	private $waterOdd = 4;
	private $lavaOdd = 4;
	private $lavaSurfaceOdd = 4;

	/**
	 * @param ChunkManager $level
	 * @param              $chunkX
	 * @param              $chunkZ
	 * @param Random       $random
	 *
	 * @return mixed|void
	 */
	public function populate(ChunkManager $level, $chunkX, $chunkZ, Random $random){
		if($random->nextRange(0, $this->waterOdd) === 0){
			$x = $random->nextRange($chunkX << 4, ($chunkX << 4) + 16);
			$y = $random->nextBoundedInt(128);
			$z = $random->nextRange($chunkZ << 4, ($chunkZ << 4) + 16);
			$pond = new \pocketmine\level\generator\object\Pond($random, new Water());
			if($pond->canPlaceObject($level, $x, $y, $z)){
				$pond->placeObject($level, $x, $y, $z);
			}
		}
	}

	/**
	 * @param $waterOdd
	 */
	public function setWaterOdd($waterOdd){
		$this->waterOdd = $waterOdd;
	}

	/**
	 * @param $lavaOdd
	 */
	public function setLavaOdd($lavaOdd){
		$this->lavaOdd = $lavaOdd;
	}

	/**
	 * @param $lavaSurfaceOdd
	 */
	public function setLavaSurfaceOdd($lavaSurfaceOdd){
		$this->lavaSurfaceOdd = $lavaSurfaceOdd;
	}
}