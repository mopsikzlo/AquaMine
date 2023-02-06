<?php

declare(strict_types=1);

namespace pocketmine\level\generator\object;

use pocketmine\block\Block;
use pocketmine\block\Wood;
use pocketmine\level\loadchunk\ChunkManager;
use pocketmine\utils\Random;

class SpruceTree extends Tree{

	protected $superBirch = \false;

	public function __construct(bool $superBirch = \false){
		$this->superBirch = $superBirch;
	}

	public function placeObject(ChunkManager $level, $x, $y, $z, Random $random){
		$this->level = $level;
		$r = mt_rand(1,2);
		if($r == 1){
			
 $this->level->setBlockIdAt($x-3, $y+2, $z+0, 18); $this->level->setBlockDataAt($x-3, $y+2, $z+0, 13);
  $this->level->setBlockIdAt($x-3, $y+3, $z-1, 18); $this->level->setBlockDataAt($x-3, $y+3, $z-1, 13);
  $this->level->setBlockIdAt($x-3, $y+5, $z+1, 18); $this->level->setBlockDataAt($x-3, $y+5, $z+1, 13);
  $this->level->setBlockIdAt($x-3, $y+7, $z+0, 18); $this->level->setBlockDataAt($x-3, $y+7, $z+0, 13);
  $this->level->setBlockIdAt($x-2, $y+2, $z+3, 18); $this->level->setBlockDataAt($x-2, $y+2, $z+3, 13);
  $this->level->setBlockIdAt($x-2, $y+3, $z-2, 18); $this->level->setBlockDataAt($x-2, $y+3, $z-2, 13);
  $this->level->setBlockIdAt($x-2, $y+3, $z+0, 18); $this->level->setBlockDataAt($x-2, $y+3, $z+0, 13);
  $this->level->setBlockIdAt($x-2, $y+3, $z+1, 18); $this->level->setBlockDataAt($x-2, $y+3, $z+1, 13);
  $this->level->setBlockIdAt($x-2, $y+5, $z-1, 18); $this->level->setBlockDataAt($x-2, $y+5, $z-1, 13);
  $this->level->setBlockIdAt($x-2, $y+6, $z+0, 18); $this->level->setBlockDataAt($x-2, $y+6, $z+0, 13);
  $this->level->setBlockIdAt($x-2, $y+6, $z+1, 18); $this->level->setBlockDataAt($x-2, $y+6, $z+1, 13);
  $this->level->setBlockIdAt($x-2, $y+7, $z-2, 18); $this->level->setBlockDataAt($x-2, $y+7, $z-2, 13);
  $this->level->setBlockIdAt($x-2, $y+7, $z+2, 18); $this->level->setBlockDataAt($x-2, $y+7, $z+2, 13);
  $this->level->setBlockIdAt($x-2, $y+8, $z-1, 18); $this->level->setBlockDataAt($x-2, $y+8, $z-1, 13);
  $this->level->setBlockIdAt($x-2, $y+8, $z+0, 18); $this->level->setBlockDataAt($x-2, $y+8, $z+0, 13);
  $this->level->setBlockIdAt($x-2, $y+8, $z+1, 18); $this->level->setBlockDataAt($x-2, $y+8, $z+1, 13);
  $this->level->setBlockIdAt($x-2, $y+9, $z+0, 18); $this->level->setBlockDataAt($x-2, $y+9, $z+0, 13);
  $this->level->setBlockIdAt($x-2, $y+10, $z+1, 18); $this->level->setBlockDataAt($x-2, $y+10, $z+1, 13);
  $this->level->setBlockIdAt($x-2, $y+12, $z+0, 18); $this->level->setBlockDataAt($x-2, $y+12, $z+0, 13);
  $this->level->setBlockIdAt($x-2, $y+12, $z+1, 18); $this->level->setBlockDataAt($x-2, $y+12, $z+1, 13);
  $this->level->setBlockIdAt($x-1, $y-2, $z+0, 162); $this->level->setBlockDataAt($x-1, $y-2, $z+0, 13);
  $this->level->setBlockIdAt($x-1, $y+1, $z+0, 18); $this->level->setBlockDataAt($x-1, $y+1, $z+0, 13);
  $this->level->setBlockIdAt($x-1, $y+1, $z+1, 18); $this->level->setBlockDataAt($x-1, $y+1, $z+1, 13);
  $this->level->setBlockIdAt($x-1, $y+2, $z-3, 18); $this->level->setBlockDataAt($x-1, $y+2, $z-3, 13);
  $this->level->setBlockIdAt($x-1, $y+3, $z-2, 18); $this->level->setBlockDataAt($x-1, $y+3, $z-2, 13);
  $this->level->setBlockIdAt($x-1, $y+3, $z+2, 18); $this->level->setBlockDataAt($x-1, $y+3, $z+2, 13);
  $this->level->setBlockIdAt($x-1, $y+3, $z+3, 18); $this->level->setBlockDataAt($x-1, $y+3, $z+3, 13);
  $this->level->setBlockIdAt($x-1, $y+4, $z+0, 18); $this->level->setBlockDataAt($x-1, $y+4, $z+0, 13);
  $this->level->setBlockIdAt($x-1, $y+4, $z+1, 18); $this->level->setBlockDataAt($x-1, $y+4, $z+1, 13);
  $this->level->setBlockIdAt($x-1, $y+5, $z-3, 18); $this->level->setBlockDataAt($x-1, $y+5, $z-3, 13);
  $this->level->setBlockIdAt($x-1, $y+5, $z+3, 18); $this->level->setBlockDataAt($x-1, $y+5, $z+3, 13);
  $this->level->setBlockIdAt($x-1, $y+6, $z-2, 18); $this->level->setBlockDataAt($x-1, $y+6, $z-2, 13);
  $this->level->setBlockIdAt($x-1, $y+6, $z-1, 18); $this->level->setBlockDataAt($x-1, $y+6, $z-1, 13);
  $this->level->setBlockIdAt($x-1, $y+6, $z+0, 18); $this->level->setBlockDataAt($x-1, $y+6, $z+0, 13);
  $this->level->setBlockIdAt($x-1, $y+6, $z+2, 18); $this->level->setBlockDataAt($x-1, $y+6, $z+2, 13);
  $this->level->setBlockIdAt($x-1, $y+8, $z-2, 18); $this->level->setBlockDataAt($x-1, $y+8, $z-2, 13);
  $this->level->setBlockIdAt($x-1, $y+8, $z-1, 18); $this->level->setBlockDataAt($x-1, $y+8, $z-1, 13);
  $this->level->setBlockIdAt($x-1, $y+8, $z+1, 18); $this->level->setBlockDataAt($x-1, $y+8, $z+1, 13);
  $this->level->setBlockIdAt($x-1, $y+9, $z+0, 18); $this->level->setBlockDataAt($x-1, $y+9, $z+0, 13);
  $this->level->setBlockIdAt($x-1, $y+9, $z+2, 18); $this->level->setBlockDataAt($x-1, $y+9, $z+2, 13);
  $this->level->setBlockIdAt($x-1, $y+10, $z-1, 18); $this->level->setBlockDataAt($x-1, $y+10, $z-1, 13);
  $this->level->setBlockIdAt($x-1, $y+10, $z+1, 18); $this->level->setBlockDataAt($x-1, $y+10, $z+1, 13);
  $this->level->setBlockIdAt($x-1, $y+11, $z+0, 18); $this->level->setBlockDataAt($x-1, $y+11, $z+0, 13);
  $this->level->setBlockIdAt($x-1, $y+12, $z+2, 18); $this->level->setBlockDataAt($x-1, $y+12, $z+2, 13);
  $this->level->setBlockIdAt($x-1, $y+13, $z-1, 18); $this->level->setBlockDataAt($x-1, $y+13, $z-1, 13);
  $this->level->setBlockIdAt($x-1, $y+13, $z+0, 18); $this->level->setBlockDataAt($x-1, $y+13, $z+0, 13);
  $this->level->setBlockIdAt($x-1, $y+14, $z+1, 18); $this->level->setBlockDataAt($x-1, $y+14, $z+1, 13);
  $this->level->setBlockIdAt($x-1, $y+15, $z+0, 18); $this->level->setBlockDataAt($x-1, $y+15, $z+0, 13);
  $this->level->setBlockIdAt($x+0, $y-2, $z-1, 162); $this->level->setBlockDataAt($x+0, $y-2, $z-1, 13);
  $this->level->setBlockIdAt($x+0, $y-2, $z+0, 162); $this->level->setBlockDataAt($x+0, $y-2, $z+0, 13);
  $this->level->setBlockIdAt($x+0, $y-2, $z+1, 162); $this->level->setBlockDataAt($x+0, $y-2, $z+1, 13);
  $this->level->setBlockIdAt($x+0, $y-1, $z-1, 162); $this->level->setBlockDataAt($x+0, $y-1, $z-1, 13);
  $this->level->setBlockIdAt($x+0, $y-1, $z+0, 162); $this->level->setBlockDataAt($x+0, $y-1, $z+0, 13);
  $this->level->setBlockIdAt($x+0, $y+0, $z+0, 162); $this->level->setBlockDataAt($x+0, $y+0, $z+0, 13);
  $this->level->setBlockIdAt($x+0, $y+1, $z-1, 18); $this->level->setBlockDataAt($x+0, $y+1, $z-1, 13);
  $this->level->setBlockIdAt($x+0, $y+1, $z+0, 162); $this->level->setBlockDataAt($x+0, $y+1, $z+0, 13);
  $this->level->setBlockIdAt($x+0, $y+2, $z+0, 162); $this->level->setBlockDataAt($x+0, $y+2, $z+0, 13);
  $this->level->setBlockIdAt($x+0, $y+2, $z+1, 18); $this->level->setBlockDataAt($x+0, $y+2, $z+1, 13);
  $this->level->setBlockIdAt($x+0, $y+3, $z-3, 18); $this->level->setBlockDataAt($x+0, $y+3, $z-3, 13);
  $this->level->setBlockIdAt($x+0, $y+3, $z-1, 18); $this->level->setBlockDataAt($x+0, $y+3, $z-1, 13);
  $this->level->setBlockIdAt($x+0, $y+3, $z+0, 162); $this->level->setBlockDataAt($x+0, $y+3, $z+0, 13);
  $this->level->setBlockIdAt($x+0, $y+3, $z+3, 18); $this->level->setBlockDataAt($x+0, $y+3, $z+3, 13);
  $this->level->setBlockIdAt($x+0, $y+4, $z-2, 18); $this->level->setBlockDataAt($x+0, $y+4, $z-2, 13);
  $this->level->setBlockIdAt($x+0, $y+4, $z-1, 18); $this->level->setBlockDataAt($x+0, $y+4, $z-1, 13);
  $this->level->setBlockIdAt($x+0, $y+4, $z+0, 162); $this->level->setBlockDataAt($x+0, $y+4, $z+0, 13);
  $this->level->setBlockIdAt($x+0, $y+4, $z+1, 18); $this->level->setBlockDataAt($x+0, $y+4, $z+1, 13);
  $this->level->setBlockIdAt($x+0, $y+4, $z+2, 18); $this->level->setBlockDataAt($x+0, $y+4, $z+2, 13);
  $this->level->setBlockIdAt($x+0, $y+5, $z+0, 162); $this->level->setBlockDataAt($x+0, $y+5, $z+0, 13);
  $this->level->setBlockIdAt($x+0, $y+6, $z-2, 18); $this->level->setBlockDataAt($x+0, $y+6, $z-2, 13);
  $this->level->setBlockIdAt($x+0, $y+6, $z+0, 162); $this->level->setBlockDataAt($x+0, $y+6, $z+0, 13);
  $this->level->setBlockIdAt($x+0, $y+6, $z+1, 18); $this->level->setBlockDataAt($x+0, $y+6, $z+1, 13);
  $this->level->setBlockIdAt($x+0, $y+6, $z+2, 18); $this->level->setBlockDataAt($x+0, $y+6, $z+2, 13);
  $this->level->setBlockIdAt($x+0, $y+7, $z-1, 18); $this->level->setBlockDataAt($x+0, $y+7, $z-1, 13);
  $this->level->setBlockIdAt($x+0, $y+7, $z+0, 162); $this->level->setBlockDataAt($x+0, $y+7, $z+0, 13);
  $this->level->setBlockIdAt($x+0, $y+7, $z+3, 18); $this->level->setBlockDataAt($x+0, $y+7, $z+3, 13);
  $this->level->setBlockIdAt($x+0, $y+8, $z-3, 18); $this->level->setBlockDataAt($x+0, $y+8, $z-3, 13);
  $this->level->setBlockIdAt($x+0, $y+8, $z-2, 18); $this->level->setBlockDataAt($x+0, $y+8, $z-2, 13);
  $this->level->setBlockIdAt($x+0, $y+8, $z+0, 162); $this->level->setBlockDataAt($x+0, $y+8, $z+0, 13);
  $this->level->setBlockIdAt($x+0, $y+8, $z+2, 18); $this->level->setBlockDataAt($x+0, $y+8, $z+2, 13);
  $this->level->setBlockIdAt($x+0, $y+9, $z-1, 18); $this->level->setBlockDataAt($x+0, $y+9, $z-1, 13);
  $this->level->setBlockIdAt($x+0, $y+9, $z+0, 162); $this->level->setBlockDataAt($x+0, $y+9, $z+0, 13);
  $this->level->setBlockIdAt($x+0, $y+9, $z+1, 18); $this->level->setBlockDataAt($x+0, $y+9, $z+1, 13);
  $this->level->setBlockIdAt($x+0, $y+10, $z+0, 162); $this->level->setBlockDataAt($x+0, $y+10, $z+0, 13);
  $this->level->setBlockIdAt($x+0, $y+11, $z-2, 18); $this->level->setBlockDataAt($x+0, $y+11, $z-2, 13);
  $this->level->setBlockIdAt($x+0, $y+11, $z-1, 18); $this->level->setBlockDataAt($x+0, $y+11, $z-1, 13);
  $this->level->setBlockIdAt($x+0, $y+11, $z+0, 162); $this->level->setBlockDataAt($x+0, $y+11, $z+0, 13);
  $this->level->setBlockIdAt($x+0, $y+11, $z+1, 18); $this->level->setBlockDataAt($x+0, $y+11, $z+1, 13);
  $this->level->setBlockIdAt($x+0, $y+12, $z+0, 18); $this->level->setBlockDataAt($x+0, $y+12, $z+0, 13);
  $this->level->setBlockIdAt($x+0, $y+12, $z+2, 18); $this->level->setBlockDataAt($x+0, $y+12, $z+2, 13);
  $this->level->setBlockIdAt($x+0, $y+13, $z-1, 18); $this->level->setBlockDataAt($x+0, $y+13, $z-1, 13);
  $this->level->setBlockIdAt($x+0, $y+13, $z+0, 18); $this->level->setBlockDataAt($x+0, $y+13, $z+0, 13);
  $this->level->setBlockIdAt($x+0, $y+13, $z+1, 18); $this->level->setBlockDataAt($x+0, $y+13, $z+1, 13);
  $this->level->setBlockIdAt($x+0, $y+14, $z+0, 18); $this->level->setBlockDataAt($x+0, $y+14, $z+0, 13);
  $this->level->setBlockIdAt($x+0, $y+15, $z-1, 18); $this->level->setBlockDataAt($x+0, $y+15, $z-1, 13);
  $this->level->setBlockIdAt($x+0, $y+15, $z+0, 18); $this->level->setBlockDataAt($x+0, $y+15, $z+0, 13);
  $this->level->setBlockIdAt($x+0, $y+16, $z+0, 18); $this->level->setBlockDataAt($x+0, $y+16, $z+0, 13);
  $this->level->setBlockIdAt($x+0, $y+16, $z+1, 18); $this->level->setBlockDataAt($x+0, $y+16, $z+1, 13);
  $this->level->setBlockIdAt($x+0, $y+17, $z+0, 18); $this->level->setBlockDataAt($x+0, $y+17, $z+0, 13);
  $this->level->setBlockIdAt($x+0, $y+18, $z+0, 18); $this->level->setBlockDataAt($x+0, $y+18, $z+0, 13);
  $this->level->setBlockIdAt($x+1, $y-2, $z-1, 162); $this->level->setBlockDataAt($x+1, $y-2, $z-1, 13);
  $this->level->setBlockIdAt($x+1, $y+1, $z+0, 18); $this->level->setBlockDataAt($x+1, $y+1, $z+0, 13);
  $this->level->setBlockIdAt($x+1, $y+3, $z-3, 18); $this->level->setBlockDataAt($x+1, $y+3, $z-3, 13);
  $this->level->setBlockIdAt($x+1, $y+3, $z-2, 18); $this->level->setBlockDataAt($x+1, $y+3, $z-2, 13);
  $this->level->setBlockIdAt($x+1, $y+3, $z+0, 18); $this->level->setBlockDataAt($x+1, $y+3, $z+0, 13);
  $this->level->setBlockIdAt($x+1, $y+4, $z-2, 18); $this->level->setBlockDataAt($x+1, $y+4, $z-2, 13);
  $this->level->setBlockIdAt($x+1, $y+4, $z-1, 18); $this->level->setBlockDataAt($x+1, $y+4, $z-1, 13);
  $this->level->setBlockIdAt($x+1, $y+4, $z+0, 18); $this->level->setBlockDataAt($x+1, $y+4, $z+0, 13);
  $this->level->setBlockIdAt($x+1, $y+4, $z+1, 18); $this->level->setBlockDataAt($x+1, $y+4, $z+1, 13);
  $this->level->setBlockIdAt($x+1, $y+4, $z+2, 18); $this->level->setBlockDataAt($x+1, $y+4, $z+2, 13);
  $this->level->setBlockIdAt($x+1, $y+4, $z+3, 18); $this->level->setBlockDataAt($x+1, $y+4, $z+3, 13);
  $this->level->setBlockIdAt($x+1, $y+5, $z-3, 18); $this->level->setBlockDataAt($x+1, $y+5, $z-3, 13);
  $this->level->setBlockIdAt($x+1, $y+5, $z+1, 18); $this->level->setBlockDataAt($x+1, $y+5, $z+1, 13);
  $this->level->setBlockIdAt($x+1, $y+6, $z-2, 18); $this->level->setBlockDataAt($x+1, $y+6, $z-2, 13);
  $this->level->setBlockIdAt($x+1, $y+6, $z+1, 18); $this->level->setBlockDataAt($x+1, $y+6, $z+1, 13);
  $this->level->setBlockIdAt($x+1, $y+6, $z+2, 18); $this->level->setBlockDataAt($x+1, $y+6, $z+2, 13);
  $this->level->setBlockIdAt($x+1, $y+7, $z-3, 18); $this->level->setBlockDataAt($x+1, $y+7, $z-3, 13);
  $this->level->setBlockIdAt($x+1, $y+7, $z+0, 18); $this->level->setBlockDataAt($x+1, $y+7, $z+0, 13);
  $this->level->setBlockIdAt($x+1, $y+8, $z-2, 18); $this->level->setBlockDataAt($x+1, $y+8, $z-2, 13);
  $this->level->setBlockIdAt($x+1, $y+8, $z+1, 18); $this->level->setBlockDataAt($x+1, $y+8, $z+1, 13);
  $this->level->setBlockIdAt($x+1, $y+8, $z+2, 18); $this->level->setBlockDataAt($x+1, $y+8, $z+2, 13);
  $this->level->setBlockIdAt($x+1, $y+9, $z-1, 18); $this->level->setBlockDataAt($x+1, $y+9, $z-1, 13);
  $this->level->setBlockIdAt($x+1, $y+9, $z+0, 18); $this->level->setBlockDataAt($x+1, $y+9, $z+0, 13);
  $this->level->setBlockIdAt($x+1, $y+10, $z-2, 18); $this->level->setBlockDataAt($x+1, $y+10, $z-2, 13);
  $this->level->setBlockIdAt($x+1, $y+10, $z+1, 18); $this->level->setBlockDataAt($x+1, $y+10, $z+1, 13);
  $this->level->setBlockIdAt($x+1, $y+11, $z+0, 18); $this->level->setBlockDataAt($x+1, $y+11, $z+0, 13);
  $this->level->setBlockIdAt($x+1, $y+12, $z-1, 18); $this->level->setBlockDataAt($x+1, $y+12, $z-1, 13);
  $this->level->setBlockIdAt($x+1, $y+13, $z+0, 18); $this->level->setBlockDataAt($x+1, $y+13, $z+0, 13);
  $this->level->setBlockIdAt($x+1, $y+14, $z+0, 18); $this->level->setBlockDataAt($x+1, $y+14, $z+0, 13);
  $this->level->setBlockIdAt($x+2, $y+3, $z-4, 18); $this->level->setBlockDataAt($x+2, $y+3, $z-4, 13);
  $this->level->setBlockIdAt($x+2, $y+3, $z-3, 18); $this->level->setBlockDataAt($x+2, $y+3, $z-3, 13);
  $this->level->setBlockIdAt($x+2, $y+3, $z+3, 18); $this->level->setBlockDataAt($x+2, $y+3, $z+3, 13);
  $this->level->setBlockIdAt($x+2, $y+4, $z-2, 18); $this->level->setBlockDataAt($x+2, $y+4, $z-2, 13);
  $this->level->setBlockIdAt($x+2, $y+4, $z-1, 18); $this->level->setBlockDataAt($x+2, $y+4, $z-1, 13);
  $this->level->setBlockIdAt($x+2, $y+4, $z+0, 18); $this->level->setBlockDataAt($x+2, $y+4, $z+0, 13);
  $this->level->setBlockIdAt($x+2, $y+4, $z+1, 18); $this->level->setBlockDataAt($x+2, $y+4, $z+1, 13);
  $this->level->setBlockIdAt($x+2, $y+5, $z+2, 18); $this->level->setBlockDataAt($x+2, $y+5, $z+2, 13);
  $this->level->setBlockIdAt($x+2, $y+6, $z-1, 18); $this->level->setBlockDataAt($x+2, $y+6, $z-1, 13);
  $this->level->setBlockIdAt($x+2, $y+7, $z+0, 18); $this->level->setBlockDataAt($x+2, $y+7, $z+0, 13);
  $this->level->setBlockIdAt($x+2, $y+7, $z+2, 18); $this->level->setBlockDataAt($x+2, $y+7, $z+2, 13);
  $this->level->setBlockIdAt($x+2, $y+8, $z+0, 18); $this->level->setBlockDataAt($x+2, $y+8, $z+0, 13);
  $this->level->setBlockIdAt($x+2, $y+8, $z+1, 18); $this->level->setBlockDataAt($x+2, $y+8, $z+1, 13);
  $this->level->setBlockIdAt($x+2, $y+10, $z-1, 18); $this->level->setBlockDataAt($x+2, $y+10, $z-1, 13);
  $this->level->setBlockIdAt($x+2, $y+11, $z+0, 18); $this->level->setBlockDataAt($x+2, $y+11, $z+0, 13);
  $this->level->setBlockIdAt($x+3, $y+2, $z-3, 18); $this->level->setBlockDataAt($x+3, $y+2, $z-3, 13);
  $this->level->setBlockIdAt($x+3, $y+3, $z-2, 18); $this->level->setBlockDataAt($x+3, $y+3, $z-2, 13);
  $this->level->setBlockIdAt($x+3, $y+3, $z-1, 18); $this->level->setBlockDataAt($x+3, $y+3, $z-1, 13);
  $this->level->setBlockIdAt($x+3, $y+3, $z+1, 18); $this->level->setBlockDataAt($x+3, $y+3, $z+1, 13);
  $this->level->setBlockIdAt($x+3, $y+4, $z+0, 18); $this->level->setBlockDataAt($x+3, $y+4, $z+0, 13);
  $this->level->setBlockIdAt($x+3, $y+5, $z-2, 18); $this->level->setBlockDataAt($x+3, $y+5, $z-2, 13);
  $this->level->setBlockIdAt($x+3, $y+6, $z-1, 18); $this->level->setBlockDataAt($x+3, $y+6, $z-1, 13);
  $this->level->setBlockIdAt($x+3, $y+6, $z+0, 18); $this->level->setBlockDataAt($x+3, $y+6, $z+0, 13);
  $this->level->setBlockIdAt($x+3, $y+7, $z+1, 18); $this->level->setBlockDataAt($x+3, $y+7, $z+1, 13);
  $this->level->setBlockIdAt($x+4, $y+3, $z-1, 18); $this->level->setBlockDataAt($x+4, $y+3, $z-1, 13);
  $this->level->setBlockIdAt($x+4, $y+6, $z+1, 18); $this->level->setBlockDataAt($x+4, $y+6, $z+1, 13);			
			
		}else{
	
  $this->level->setBlockIdAt($x-2, $y+3, $z-1, 18); $this->level->setBlockDataAt($x-2, $y+3, $z-1, 13);
  $this->level->setBlockIdAt($x-1, $y-1, $z+1, 162); $this->level->setBlockDataAt($x-1, $y-1, $z+1, 13);
  $this->level->setBlockIdAt($x-1, $y+2, $z-2, 18); $this->level->setBlockDataAt($x-1, $y+2, $z-2, 13);
  $this->level->setBlockIdAt($x-1, $y+2, $z+1, 18); $this->level->setBlockDataAt($x-1, $y+2, $z+1, 13);
  $this->level->setBlockIdAt($x-1, $y+3, $z+0, 18); $this->level->setBlockDataAt($x-1, $y+3, $z+0, 13);
  $this->level->setBlockIdAt($x-1, $y+4, $z-1, 18); $this->level->setBlockDataAt($x-1, $y+4, $z-1, 13);
  $this->level->setBlockIdAt($x-1, $y+5, $z-2, 18); $this->level->setBlockDataAt($x-1, $y+5, $z-2, 13);
  $this->level->setBlockIdAt($x-1, $y+5, $z+0, 18); $this->level->setBlockDataAt($x-1, $y+5, $z+0, 13);
  $this->level->setBlockIdAt($x-1, $y+6, $z-1, 18); $this->level->setBlockDataAt($x-1, $y+6, $z-1, 13);
  $this->level->setBlockIdAt($x-1, $y+7, $z-2, 18); $this->level->setBlockDataAt($x-1, $y+7, $z-2, 13);
  $this->level->setBlockIdAt($x-1, $y+7, $z+0, 18); $this->level->setBlockDataAt($x-1, $y+7, $z+0, 13);
  $this->level->setBlockIdAt($x-1, $y+8, $z-1, 18); $this->level->setBlockDataAt($x-1, $y+8, $z-1, 13);
  $this->level->setBlockIdAt($x+0, $y+0, $z+0, 162); $this->level->setBlockDataAt($x+0, $y+0, $z+0, 13);
  $this->level->setBlockIdAt($x+0, $y+1, $z+0, 162); $this->level->setBlockDataAt($x+0, $y+1, $z+0, 13);
  $this->level->setBlockIdAt($x+0, $y+2, $z+0, 162); $this->level->setBlockDataAt($x+0, $y+2, $z+0, 13);
  $this->level->setBlockIdAt($x+0, $y+3, $z-2, 18); $this->level->setBlockDataAt($x+0, $y+3, $z-2, 13);
  $this->level->setBlockIdAt($x+0, $y+3, $z+0, 162); $this->level->setBlockDataAt($x+0, $y+3, $z+0, 13);
  $this->level->setBlockIdAt($x+0, $y+3, $z+2, 18); $this->level->setBlockDataAt($x+0, $y+3, $z+2, 13);
  $this->level->setBlockIdAt($x+0, $y+4, $z-1, 162); $this->level->setBlockDataAt($x+0, $y+4, $z-1, 13);
  $this->level->setBlockIdAt($x+0, $y+4, $z+0, 18); $this->level->setBlockDataAt($x+0, $y+4, $z+0, 13);
  $this->level->setBlockIdAt($x+0, $y+4, $z+1, 18); $this->level->setBlockDataAt($x+0, $y+4, $z+1, 13);
  $this->level->setBlockIdAt($x+0, $y+5, $z-2, 18); $this->level->setBlockDataAt($x+0, $y+5, $z-2, 13);
  $this->level->setBlockIdAt($x+0, $y+5, $z-1, 162); $this->level->setBlockDataAt($x+0, $y+5, $z-1, 13);
  $this->level->setBlockIdAt($x+0, $y+5, $z+0, 18); $this->level->setBlockDataAt($x+0, $y+5, $z+0, 13);
  $this->level->setBlockIdAt($x+0, $y+6, $z-2, 18); $this->level->setBlockDataAt($x+0, $y+6, $z-2, 13);
  $this->level->setBlockIdAt($x+0, $y+6, $z-1, 18); $this->level->setBlockDataAt($x+0, $y+6, $z-1, 13);
  $this->level->setBlockIdAt($x+0, $y+6, $z+0, 18); $this->level->setBlockDataAt($x+0, $y+6, $z+0, 13);
  $this->level->setBlockIdAt($x+0, $y+6, $z+1, 18); $this->level->setBlockDataAt($x+0, $y+6, $z+1, 13);
  $this->level->setBlockIdAt($x+0, $y+7, $z-1, 18); $this->level->setBlockDataAt($x+0, $y+7, $z-1, 13);
  $this->level->setBlockIdAt($x+0, $y+8, $z-2, 18); $this->level->setBlockDataAt($x+0, $y+8, $z-2, 13);
  $this->level->setBlockIdAt($x+0, $y+8, $z-1, 18); $this->level->setBlockDataAt($x+0, $y+8, $z-1, 13);
  $this->level->setBlockIdAt($x+0, $y+8, $z+0, 18); $this->level->setBlockDataAt($x+0, $y+8, $z+0, 13);
  $this->level->setBlockIdAt($x+0, $y+9, $z-1, 18); $this->level->setBlockDataAt($x+0, $y+9, $z-1, 13);
  $this->level->setBlockIdAt($x+0, $y+10, $z-1, 18); $this->level->setBlockDataAt($x+0, $y+10, $z-1, 13);
  $this->level->setBlockIdAt($x+1, $y-1, $z+0, 162); $this->level->setBlockDataAt($x+1, $y-1, $z+0, 13);
  $this->level->setBlockIdAt($x+1, $y+3, $z-2, 18); $this->level->setBlockDataAt($x+1, $y+3, $z-2, 13);
  $this->level->setBlockIdAt($x+1, $y+3, $z+0, 18); $this->level->setBlockDataAt($x+1, $y+3, $z+0, 13);
  $this->level->setBlockIdAt($x+1, $y+3, $z+1, 18); $this->level->setBlockDataAt($x+1, $y+3, $z+1, 13);
  $this->level->setBlockIdAt($x+1, $y+4, $z-1, 18); $this->level->setBlockDataAt($x+1, $y+4, $z-1, 13);
  $this->level->setBlockIdAt($x+1, $y+4, $z+0, 18); $this->level->setBlockDataAt($x+1, $y+4, $z+0, 13);
  $this->level->setBlockIdAt($x+1, $y+6, $z-1, 18); $this->level->setBlockDataAt($x+1, $y+6, $z-1, 13);
  $this->level->setBlockIdAt($x+1, $y+6, $z+0, 18); $this->level->setBlockDataAt($x+1, $y+6, $z+0, 13);
  $this->level->setBlockIdAt($x+1, $y+7, $z-1, 18); $this->level->setBlockDataAt($x+1, $y+7, $z-1, 13);
  $this->level->setBlockIdAt($x+2, $y+2, $z+0, 18); $this->level->setBlockDataAt($x+2, $y+2, $z+0, 13);
  $this->level->setBlockIdAt($x+2, $y+3, $z-1, 18); $this->level->setBlockDataAt($x+2, $y+3, $z-1, 13);				
			
		}
	}

}

?>