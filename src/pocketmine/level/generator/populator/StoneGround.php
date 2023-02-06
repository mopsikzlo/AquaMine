<?php

declare(strict_types=1);

namespace pocketmine\level\generator\populator;

use pocketmine\block\Block;
use pocketmine\level\loadchunk\ChunkManager;
use pocketmine\level\generator\populator\VariableAmountPopulator;
use pocketmine\utils\Random;

class StoneGround extends VariableAmountPopulator{
	/** @var ChunkManager */
	private $level;

	public function __construct(){
		parent::__construct(1, 0, 64);
	}

	public function populate(ChunkManager $level, $chunkX, $chunkZ, Random $random){
		if(!$this->checkOdd($random)){
			return;
		}
		$this->level = $level;
		$amount = $this->getAmount($random);

		for($i = 0; $i < $amount; ++$i){
			$x = $chunkX * 16;
			$z = $chunkZ * 16;
			for($size = 35; $size > 0; $size--){
				$xx = $x - 7 + $random->nextRange(0, 15);
				$zz = $z - 7 + $random->nextRange(0, 15);
				$yy = $this->getHighestWorkableBlock($xx, $zz);
				if($yy !== -1 and $this->canStoneGroundStay($xx, $yy, $zz)){
				$this->level->setBlockIdAt($xx, $yy-1, $zz, 3);
				$this->level->setBlockDataAt($xx, $yy-1, $zz, 0);
             }
				}
			}
		}

	private function canStoneGroundStay($x, $y, $z){
		$c = $this->level->getBlockIdAt($x, $y, $z);
		$b = $this->level->getBlockIdAt($x, $y - 1, $z);
		return ($b === Block::AIR or $b === Block::SNOW_LAYER) and $this->level->getBlockIdAt($x, $y - 1, $z) === Block::DIRT;
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