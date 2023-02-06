<?php

namespace pocketmine\level\generator\populator;

use pocketmine\level\loadchunk\ChunkManager;
use pocketmine\utils\Random;

class Mineshaft extends Populator {
	private static $ODD = 3;
	/**
	 * @param ChunkManager $level
	 * @param              $chunkX
	 * @param              $chunkZ
	 * @param Random       $random
	 *
	 * @return mixed|void
	 */
	public function populate(ChunkManager $level, $chunkX, $chunkZ, Random $random){
		if($random->nextRange(0, self::$ODD) === 0){
			//$mineshaft = new Mineshaft($random);
		}
	}

}