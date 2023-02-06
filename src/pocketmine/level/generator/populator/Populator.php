<?php

namespace pocketmine\level\generator\populator;

use pocketmine\level\loadchunk\ChunkManager;
use pocketmine\utils\Random;

abstract class Populator {
	/**
	 * @param ChunkManager $level
	 * @param              $chunkX
	 * @param              $chunkZ
	 * @param Random       $random
	 *
	 * @return mixed
	 */
	public abstract function populate(ChunkManager $level, $chunkX, $chunkZ, Random $random);
}