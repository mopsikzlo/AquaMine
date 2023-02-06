<?php

namespace pocketmine\level\generator\populator;

use pocketmine\level\loadchunk\ChunkManager;
use pocketmine\level\generator\object\NetherOre as ObjectOre;
use pocketmine\utils\Random;

class NetherOre extends Populator {
	private $oreTypes = [];

	/**
	 * @param ChunkManager $level
	 * @param              $chunkX
	 * @param              $chunkZ
	 * @param Random       $random
	 *
	 * @return mixed|void
	 */
	public function populate(ChunkManager $level, $chunkX, $chunkZ, Random $random){
		foreach($this->oreTypes as $type){
			$ore = new ObjectOre($random, $type);
			for($i = 0; $i < $ore->type->clusterCount; ++$i){
				$x = $random->nextRange($chunkX << 4, ($chunkX << 4) + 15);
				$y = $random->nextRange($ore->type->minHeight, $ore->type->maxHeight);
				$z = $random->nextRange($chunkZ << 4, ($chunkZ << 4) + 15);
				if($ore->canPlaceObject($level, $x, $y, $z)){
					$ore->placeObject($level, $x, $y, $z);
				}
			}
		}
	}

	/**
	 * @param array $types
	 */
	public function setOreTypes(array $types){
		$this->oreTypes = $types;
	}
}