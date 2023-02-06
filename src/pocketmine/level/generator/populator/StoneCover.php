<?php

declare(strict_types=1);

namespace pocketmine\level\generator\populator;

use pocketmine\block\Block;
use pocketmine\level\loadchunk\ChunkManager;
use pocketmine\utils\Random;
use pocketmine\level\generator\populator\VariableAmountPopulator;

class StoneCover extends VariableAmountPopulator{
	/** @var ChunkManager */
	private $level;

	public function populate(ChunkManager $level, $chunkX, $chunkZ, Random $random){
		$this->level = $level;
		$amount = $this->getAmount($random);
		for($i = 0; $i < $amount; ++$i){
			$x = $random->nextRange($chunkX * 16, $chunkX * 16 + 15);
			$z = $random->nextRange($chunkZ * 16, $chunkZ * 16 + 15);
			$y = $this->getHighestWorkableBlock($x, $z);

			if($y !== -1 and $this->canStoneCoverStay($x, $y, $z)) {
				$rnd = mt_rand(1,7);
				if($rnd == 1) {
					$this->level->setBlockIdAt($x, $y-1, $z, 1);
					$this->level->setBlockDataAt($x, $y-1, $z, 0);
				}elseif($rnd == 2) {
					$this->level->setBlockIdAt($x, $y-1, $z, 1);
					$this->level->setBlockDataAt($x, $y-1, $z, 5);
				}elseif($rnd == 3) {
					$this->level->setBlockIdAt($x, $y-1, $z, 13);
					$this->level->setBlockDataAt($x, $y-1, $z, 0);
				}elseif($rnd == 4) {
					$this->level->setBlockIdAt($x, $y-1, $z, 4);
					$this->level->setBlockDataAt($x, $y-1, $z, 0);
				}elseif($rnd == 5) {
					$this->level->setBlockIdAt($x, $y-1, $z, 3);
					$this->level->setBlockDataAt($x, $y-1, $z, 0);
          }elseif($rnd == 6){
					$this->level->setBlockIdAt($x, $y-1, $z, 98);
					$this->level->setBlockDataAt($x, $y-1, $z, 2);
				}else{
					$this->level->setBlockIdAt($x, $y-1, $z, 237);
					$this->level->setBlockDataAt($x, $y-1, $z, 12);					
				}				
			}
		}
	}

	private function canStoneCoverStay($x, $y, $z){
		$b = $this->level->getBlockIdAt($x, $y, $z);
		return ($b === Block::AIR or $b === Block::SNOW_LAYER) and $this->level->getBlockIdAt($x, $y - 1, $z) === Block::GRASS;
	}

	private function getHighestWorkableBlock($x, $z){
		for($y = 127; $y >= 0; --$y){
			$b = $this->level->getBlockIdAt($x, $y, $z);
			if($b !== Block::AIR and $b !== Block::LEAVES and $b !== Block::LEAVES2 and $b !== Block::SNOW_LAYER){
				break;
			}
		}

		return $y === 0 ? -1 : ++$y;
	}
}
