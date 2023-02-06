<?php

declare(strict_types = 1);

namespace pocketmine\level\generator\object;

use pocketmine\block\Block;
use pocketmine\level\loadchunk\ChunkManager;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;

class Pond {
	private $random;
	public $type;

	/**
	 * Pond constructor.
	 *
	 * @param Random $random
	 * @param Block  $type
	 */
	public function __construct(Random $random, Block $type){
		$this->type = $type;
		$this->random = $random;
	}

	/**
	 * @param ChunkManager $level
	 * @param Vector3      $pos
	 */
	public function canPlaceObject(ChunkManager $level, Vector3 $pos){
	}

	/**
	 * @param ChunkManager $level
	 * @param Vector3      $pos
	 */
	public function placeObject(ChunkManager $level, Vector3 $pos){
	}

}