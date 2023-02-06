<?php

declare(strict_types=1);

namespace pocketmine\level\generator\object;

use pocketmine\block\Block;
use pocketmine\block\Wood;
use pocketmine\level\ChunkManager;
use pocketmine\utils\Random;

class AcaciaTree extends Tree{

	protected $superBirch = \false;

	public function __construct(bool $superBirch = \false){
		$this->superBirch = $superBirch;
	}

	public function placeObject(ChunkManager $level, $x, $y, $z, Random $random){
		$this->level = $level;
		
  $this->level->setBlockIdAt($x-4, $y+7, $z-2, 161); $this->level->setBlockDataAt($x-4, $y+7, $z-2, 0);
  $this->level->setBlockIdAt($x-4, $y+7, $z-1, 161); $this->level->setBlockDataAt($x-4, $y+7, $z-1, 0);
  $this->level->setBlockIdAt($x-4, $y+9, $z-1, 161); $this->level->setBlockDataAt($x-4, $y+9, $z-1, 0);
  $this->level->setBlockIdAt($x-3, $y+5, $z+3, 161); $this->level->setBlockDataAt($x-3, $y+5, $z+3, 0);
  $this->level->setBlockIdAt($x-3, $y+6, $z+3, 161); $this->level->setBlockDataAt($x-3, $y+6, $z+3, 0);
  $this->level->setBlockIdAt($x-3, $y+6, $z+4, 161); $this->level->setBlockDataAt($x-3, $y+6, $z+4, 0);
  $this->level->setBlockIdAt($x-3, $y+7, $z-3, 161); $this->level->setBlockDataAt($x-3, $y+7, $z-3, 0);
  $this->level->setBlockIdAt($x-3, $y+9, $z-3, 161); $this->level->setBlockDataAt($x-3, $y+9, $z-3, 0);
  $this->level->setBlockIdAt($x-3, $y+9, $z+0, 161); $this->level->setBlockDataAt($x-3, $y+9, $z+0, 0);
  $this->level->setBlockIdAt($x-2, $y+3, $z+4, 161); $this->level->setBlockDataAt($x-2, $y+3, $z+4, 0);
  $this->level->setBlockIdAt($x-2, $y+4, $z+4, 161); $this->level->setBlockDataAt($x-2, $y+4, $z+4, 0);
  $this->level->setBlockIdAt($x-2, $y+5, $z-2, 161); $this->level->setBlockDataAt($x-2, $y+5, $z-2, 0);
  $this->level->setBlockIdAt($x-2, $y+5, $z+2, 161); $this->level->setBlockDataAt($x-2, $y+5, $z+2, 0);
  $this->level->setBlockIdAt($x-2, $y+5, $z+4, 161); $this->level->setBlockDataAt($x-2, $y+5, $z+4, 0);
  $this->level->setBlockIdAt($x-2, $y+5, $z+5, 161); $this->level->setBlockDataAt($x-2, $y+5, $z+5, 0);
  $this->level->setBlockIdAt($x-2, $y+5, $z+6, 161); $this->level->setBlockDataAt($x-2, $y+5, $z+6, 0);
  $this->level->setBlockIdAt($x-2, $y+6, $z-3, 161); $this->level->setBlockDataAt($x-2, $y+6, $z-3, 0);
  $this->level->setBlockIdAt($x-2, $y+6, $z+2, 161); $this->level->setBlockDataAt($x-2, $y+6, $z+2, 0);
  $this->level->setBlockIdAt($x-2, $y+6, $z+4, 161); $this->level->setBlockDataAt($x-2, $y+6, $z+4, 0);
  $this->level->setBlockIdAt($x-2, $y+6, $z+5, 161); $this->level->setBlockDataAt($x-2, $y+6, $z+5, 0);
  $this->level->setBlockIdAt($x-2, $y+7, $z-4, 161); $this->level->setBlockDataAt($x-2, $y+7, $z-4, 0);
  $this->level->setBlockIdAt($x-2, $y+7, $z-1, 162); $this->level->setBlockDataAt($x-2, $y+7, $z-1, 0);
  $this->level->setBlockIdAt($x-2, $y+8, $z-3, 161); $this->level->setBlockDataAt($x-2, $y+8, $z-3, 0);
  $this->level->setBlockIdAt($x-2, $y+8, $z-2, 161); $this->level->setBlockDataAt($x-2, $y+8, $z-2, 0);
  $this->level->setBlockIdAt($x-2, $y+8, $z-1, 162); $this->level->setBlockDataAt($x-2, $y+8, $z-1, 0);
  $this->level->setBlockIdAt($x-2, $y+8, $z+0, 161); $this->level->setBlockDataAt($x-2, $y+8, $z+0, 0);
  $this->level->setBlockIdAt($x-2, $y+8, $z+1, 161); $this->level->setBlockDataAt($x-2, $y+8, $z+1, 0);
  $this->level->setBlockIdAt($x-1, $y+3, $z+3, 161); $this->level->setBlockDataAt($x-1, $y+3, $z+3, 0);
  $this->level->setBlockIdAt($x-1, $y+3, $z+4, 161); $this->level->setBlockDataAt($x-1, $y+3, $z+4, 0);
  $this->level->setBlockIdAt($x-1, $y+3, $z+5, 161); $this->level->setBlockDataAt($x-1, $y+3, $z+5, 0);
  $this->level->setBlockIdAt($x-1, $y+4, $z-4, 161); $this->level->setBlockDataAt($x-1, $y+4, $z-4, 0);
  $this->level->setBlockIdAt($x-1, $y+4, $z+2, 162); $this->level->setBlockDataAt($x-1, $y+4, $z+2, 0);
  $this->level->setBlockIdAt($x-1, $y+4, $z+3, 162); $this->level->setBlockDataAt($x-1, $y+4, $z+3, 0);
  $this->level->setBlockIdAt($x-1, $y+4, $z+4, 161); $this->level->setBlockDataAt($x-1, $y+4, $z+4, 0);
  $this->level->setBlockIdAt($x-1, $y+4, $z+5, 161); $this->level->setBlockDataAt($x-1, $y+4, $z+5, 0);
  $this->level->setBlockIdAt($x-1, $y+5, $z-5, 161); $this->level->setBlockDataAt($x-1, $y+5, $z-5, 0);
  $this->level->setBlockIdAt($x-1, $y+5, $z-3, 161); $this->level->setBlockDataAt($x-1, $y+5, $z-3, 0);
  $this->level->setBlockIdAt($x-1, $y+5, $z+4, 162); $this->level->setBlockDataAt($x-1, $y+5, $z+4, 0);
  $this->level->setBlockIdAt($x-1, $y+6, $z+0, 162); $this->level->setBlockDataAt($x-1, $y+6, $z+0, 0);
  $this->level->setBlockIdAt($x-1, $y+6, $z+3, 161); $this->level->setBlockDataAt($x-1, $y+6, $z+3, 0);
  $this->level->setBlockIdAt($x-1, $y+6, $z+4, 162); $this->level->setBlockDataAt($x-1, $y+6, $z+4, 0);
  $this->level->setBlockIdAt($x-1, $y+6, $z+5, 161); $this->level->setBlockDataAt($x-1, $y+6, $z+5, 0);
  $this->level->setBlockIdAt($x-1, $y+6, $z+6, 161); $this->level->setBlockDataAt($x-1, $y+6, $z+6, 0);
  $this->level->setBlockIdAt($x-1, $y+7, $z-3, 161); $this->level->setBlockDataAt($x-1, $y+7, $z-3, 0);
  $this->level->setBlockIdAt($x-1, $y+7, $z+3, 161); $this->level->setBlockDataAt($x-1, $y+7, $z+3, 0);
  $this->level->setBlockIdAt($x-1, $y+7, $z+6, 161); $this->level->setBlockDataAt($x-1, $y+7, $z+6, 0);
  $this->level->setBlockIdAt($x-1, $y+8, $z+0, 161); $this->level->setBlockDataAt($x-1, $y+8, $z+0, 0);
  $this->level->setBlockIdAt($x-1, $y+8, $z+1, 161); $this->level->setBlockDataAt($x-1, $y+8, $z+1, 0);
  $this->level->setBlockIdAt($x+0, $y+0, $z+0, 162); $this->level->setBlockDataAt($x+0, $y+0, $z+0, 0);
  $this->level->setBlockIdAt($x+0, $y+1, $z+0, 162); $this->level->setBlockDataAt($x+0, $y+1, $z+0, 0);
  $this->level->setBlockIdAt($x+0, $y+2, $z+0, 162); $this->level->setBlockDataAt($x+0, $y+2, $z+0, 0);
  $this->level->setBlockIdAt($x+0, $y+3, $z-1, 162); $this->level->setBlockDataAt($x+0, $y+3, $z-1, 0);
  $this->level->setBlockIdAt($x+0, $y+3, $z+0, 162); $this->level->setBlockDataAt($x+0, $y+3, $z+0, 0);
  $this->level->setBlockIdAt($x+0, $y+3, $z+1, 162); $this->level->setBlockDataAt($x+0, $y+3, $z+1, 0);
  $this->level->setBlockIdAt($x+0, $y+4, $z-4, 161); $this->level->setBlockDataAt($x+0, $y+4, $z-4, 0);
  $this->level->setBlockIdAt($x+0, $y+4, $z-3, 161); $this->level->setBlockDataAt($x+0, $y+4, $z-3, 0);
  $this->level->setBlockIdAt($x+0, $y+4, $z+0, 162); $this->level->setBlockDataAt($x+0, $y+4, $z+0, 0);
  $this->level->setBlockIdAt($x+0, $y+5, $z-3, 161); $this->level->setBlockDataAt($x+0, $y+5, $z-3, 0);
  $this->level->setBlockIdAt($x+0, $y+5, $z+0, 162); $this->level->setBlockDataAt($x+0, $y+5, $z+0, 0);
  $this->level->setBlockIdAt($x+0, $y+5, $z+3, 161); $this->level->setBlockDataAt($x+0, $y+5, $z+3, 0);
  $this->level->setBlockIdAt($x+0, $y+5, $z+5, 161); $this->level->setBlockDataAt($x+0, $y+5, $z+5, 0);
  $this->level->setBlockIdAt($x+0, $y+6, $z-5, 161); $this->level->setBlockDataAt($x+0, $y+6, $z-5, 0);
  $this->level->setBlockIdAt($x+0, $y+6, $z-3, 162); $this->level->setBlockDataAt($x+0, $y+6, $z-3, 0);
  $this->level->setBlockIdAt($x+0, $y+6, $z-2, 161); $this->level->setBlockDataAt($x+0, $y+6, $z-2, 0);
  $this->level->setBlockIdAt($x+0, $y+6, $z-1, 161); $this->level->setBlockDataAt($x+0, $y+6, $z-1, 0);
  $this->level->setBlockIdAt($x+0, $y+6, $z+2, 161); $this->level->setBlockDataAt($x+0, $y+6, $z+2, 0);
  $this->level->setBlockIdAt($x+0, $y+6, $z+4, 161); $this->level->setBlockDataAt($x+0, $y+6, $z+4, 0);
  $this->level->setBlockIdAt($x+0, $y+7, $z-3, 161); $this->level->setBlockDataAt($x+0, $y+7, $z-3, 0);
  $this->level->setBlockIdAt($x+0, $y+7, $z-2, 161); $this->level->setBlockDataAt($x+0, $y+7, $z-2, 0);
  $this->level->setBlockIdAt($x+0, $y+7, $z+1, 161); $this->level->setBlockDataAt($x+0, $y+7, $z+1, 0);
  $this->level->setBlockIdAt($x+0, $y+7, $z+2, 161); $this->level->setBlockDataAt($x+0, $y+7, $z+2, 0);
  $this->level->setBlockIdAt($x+0, $y+7, $z+3, 161); $this->level->setBlockDataAt($x+0, $y+7, $z+3, 0);
  $this->level->setBlockIdAt($x+0, $y+7, $z+5, 161); $this->level->setBlockDataAt($x+0, $y+7, $z+5, 0);
  $this->level->setBlockIdAt($x+0, $y+8, $z-2, 161); $this->level->setBlockDataAt($x+0, $y+8, $z-2, 0);
  $this->level->setBlockIdAt($x+0, $y+8, $z-1, 161); $this->level->setBlockDataAt($x+0, $y+8, $z-1, 0);
  $this->level->setBlockIdAt($x+0, $y+8, $z+0, 161); $this->level->setBlockDataAt($x+0, $y+8, $z+0, 0);
  $this->level->setBlockIdAt($x+0, $y+8, $z+3, 161); $this->level->setBlockDataAt($x+0, $y+8, $z+3, 0);
  $this->level->setBlockIdAt($x+0, $y+8, $z+5, 161); $this->level->setBlockDataAt($x+0, $y+8, $z+5, 0);
  $this->level->setBlockIdAt($x+0, $y+9, $z-1, 161); $this->level->setBlockDataAt($x+0, $y+9, $z-1, 0);
  $this->level->setBlockIdAt($x+0, $y+9, $z+3, 161); $this->level->setBlockDataAt($x+0, $y+9, $z+3, 0);
  $this->level->setBlockIdAt($x+1, $y+4, $z-3, 161); $this->level->setBlockDataAt($x+1, $y+4, $z-3, 0);
  $this->level->setBlockIdAt($x+1, $y+4, $z-2, 162); $this->level->setBlockDataAt($x+1, $y+4, $z-2, 0);
  $this->level->setBlockIdAt($x+1, $y+4, $z+3, 161); $this->level->setBlockDataAt($x+1, $y+4, $z+3, 0);
  $this->level->setBlockIdAt($x+1, $y+5, $z-3, 161); $this->level->setBlockDataAt($x+1, $y+5, $z-3, 0);
  $this->level->setBlockIdAt($x+1, $y+5, $z-2, 162); $this->level->setBlockDataAt($x+1, $y+5, $z-2, 0);
  $this->level->setBlockIdAt($x+1, $y+5, $z+3, 161); $this->level->setBlockDataAt($x+1, $y+5, $z+3, 0);
  $this->level->setBlockIdAt($x+1, $y+5, $z+4, 161); $this->level->setBlockDataAt($x+1, $y+5, $z+4, 0);
  $this->level->setBlockIdAt($x+1, $y+6, $z-3, 161); $this->level->setBlockDataAt($x+1, $y+6, $z-3, 0);
  $this->level->setBlockIdAt($x+1, $y+6, $z-2, 161); $this->level->setBlockDataAt($x+1, $y+6, $z-2, 0);
  $this->level->setBlockIdAt($x+1, $y+6, $z+0, 161); $this->level->setBlockDataAt($x+1, $y+6, $z+0, 0);
  $this->level->setBlockIdAt($x+1, $y+6, $z+1, 162); $this->level->setBlockDataAt($x+1, $y+6, $z+1, 0);
  $this->level->setBlockIdAt($x+1, $y+6, $z+3, 161); $this->level->setBlockDataAt($x+1, $y+6, $z+3, 0);
  $this->level->setBlockIdAt($x+1, $y+6, $z+4, 161); $this->level->setBlockDataAt($x+1, $y+6, $z+4, 0);
  $this->level->setBlockIdAt($x+1, $y+6, $z+5, 161); $this->level->setBlockDataAt($x+1, $y+6, $z+5, 0);
  $this->level->setBlockIdAt($x+1, $y+7, $z-1, 161); $this->level->setBlockDataAt($x+1, $y+7, $z-1, 0);
  $this->level->setBlockIdAt($x+1, $y+7, $z+0, 161); $this->level->setBlockDataAt($x+1, $y+7, $z+0, 0);
  $this->level->setBlockIdAt($x+1, $y+8, $z-2, 161); $this->level->setBlockDataAt($x+1, $y+8, $z-2, 0);
  $this->level->setBlockIdAt($x+1, $y+9, $z+0, 161); $this->level->setBlockDataAt($x+1, $y+9, $z+0, 0);
  $this->level->setBlockIdAt($x+1, $y+9, $z+4, 161); $this->level->setBlockDataAt($x+1, $y+9, $z+4, 0);
  $this->level->setBlockIdAt($x+2, $y+5, $z-3, 161); $this->level->setBlockDataAt($x+2, $y+5, $z-3, 0);
  $this->level->setBlockIdAt($x+2, $y+5, $z-2, 161); $this->level->setBlockDataAt($x+2, $y+5, $z-2, 0);
  $this->level->setBlockIdAt($x+2, $y+6, $z-4, 161); $this->level->setBlockDataAt($x+2, $y+6, $z-4, 0);
  $this->level->setBlockIdAt($x+2, $y+6, $z-2, 162); $this->level->setBlockDataAt($x+2, $y+6, $z-2, 0);
  $this->level->setBlockIdAt($x+2, $y+6, $z+3, 161); $this->level->setBlockDataAt($x+2, $y+6, $z+3, 0);
  $this->level->setBlockIdAt($x+2, $y+7, $z-3, 161); $this->level->setBlockDataAt($x+2, $y+7, $z-3, 0);
  $this->level->setBlockIdAt($x+2, $y+7, $z+1, 162); $this->level->setBlockDataAt($x+2, $y+7, $z+1, 0);
  $this->level->setBlockIdAt($x+2, $y+7, $z+3, 161); $this->level->setBlockDataAt($x+2, $y+7, $z+3, 0);
  $this->level->setBlockIdAt($x+2, $y+7, $z+4, 161); $this->level->setBlockDataAt($x+2, $y+7, $z+4, 0);
  $this->level->setBlockIdAt($x+2, $y+8, $z-2, 161); $this->level->setBlockDataAt($x+2, $y+8, $z-2, 0);
  $this->level->setBlockIdAt($x+2, $y+8, $z-1, 161); $this->level->setBlockDataAt($x+2, $y+8, $z-1, 0);
  $this->level->setBlockIdAt($x+2, $y+8, $z+2, 162); $this->level->setBlockDataAt($x+2, $y+8, $z+2, 0);
  $this->level->setBlockIdAt($x+2, $y+9, $z+0, 161); $this->level->setBlockDataAt($x+2, $y+9, $z+0, 0);
  $this->level->setBlockIdAt($x+2, $y+9, $z+2, 161); $this->level->setBlockDataAt($x+2, $y+9, $z+2, 0);
  $this->level->setBlockIdAt($x+3, $y+4, $z-3, 161); $this->level->setBlockDataAt($x+3, $y+4, $z-3, 0);
  $this->level->setBlockIdAt($x+3, $y+4, $z-2, 161); $this->level->setBlockDataAt($x+3, $y+4, $z-2, 0);
  $this->level->setBlockIdAt($x+3, $y+5, $z-2, 161); $this->level->setBlockDataAt($x+3, $y+5, $z-2, 0);
  $this->level->setBlockIdAt($x+3, $y+6, $z-2, 161); $this->level->setBlockDataAt($x+3, $y+6, $z-2, 0);
  $this->level->setBlockIdAt($x+3, $y+6, $z+0, 161); $this->level->setBlockDataAt($x+3, $y+6, $z+0, 0);
  $this->level->setBlockIdAt($x+3, $y+6, $z+3, 161); $this->level->setBlockDataAt($x+3, $y+6, $z+3, 0);
  $this->level->setBlockIdAt($x+3, $y+7, $z+2, 161); $this->level->setBlockDataAt($x+3, $y+7, $z+2, 0);
  $this->level->setBlockIdAt($x+3, $y+7, $z+3, 161); $this->level->setBlockDataAt($x+3, $y+7, $z+3, 0);
  $this->level->setBlockIdAt($x+3, $y+8, $z-2, 161); $this->level->setBlockDataAt($x+3, $y+8, $z-2, 0);
  $this->level->setBlockIdAt($x+3, $y+8, $z+2, 161); $this->level->setBlockDataAt($x+3, $y+8, $z+2, 0);
  $this->level->setBlockIdAt($x+3, $y+8, $z+4, 161); $this->level->setBlockDataAt($x+3, $y+8, $z+4, 0);
  $this->level->setBlockIdAt($x+4, $y+7, $z+3, 161); $this->level->setBlockDataAt($x+4, $y+7, $z+3, 0);
  $this->level->setBlockIdAt($x+4, $y+8, $z+1, 161); $this->level->setBlockDataAt($x+4, $y+8, $z+1, 0);

		}
		
}

?>