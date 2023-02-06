<?php

namespace pocketmine\level\generator\populator;

use pocketmine\block\Block;
use pocketmine\block\Sapling;
use pocketmine\level\loadchunk\ChunkManager;
use pocketmine\level\generator\object\Tree as ObjectTree;
use pocketmine\utils\Random;

class Tree extends Populator {
	/** @var ChunkManager */
	private $level;
	private $randomAmount = 1;
	private $baseAmount = 0;

	private $type;

	/**
	 * Tree constructor.
	 *
	 * @param int $type
	 */
	public function __construct($type = Sapling::OAK){
		$this->type = $type;
	}

	/**
	 * @param $amount
	 */
	public function setRandomAmount($amount){
		$this->randomAmount = $amount;
	}

	/**
	 * @param $amount
	 */
	public function setBaseAmount($amount){
		$this->baseAmount = $amount;
	}

	/**
	 * @param ChunkManager $level
	 * @param              $chunkX
	 * @param              $chunkZ
	 * @param Random       $random
	 *
	 * @return mixed|void
	 */
	public function populate(ChunkManager $level, $chunkX, $chunkZ, Random $random){
		$this->level = $level;
		$amount = $random->nextRange(0, $this->randomAmount) + $this->baseAmount;
		for($i = 0; $i < $amount; ++$i){
			$x = $random->nextRange($chunkX << 4, ($chunkX << 4) + 15);
			$z = $random->nextRange($chunkZ << 4, ($chunkZ << 4) + 15);
			$y = $this->getHighestWorkableBlock($x, $z);
			if($y === -1){
				continue;
			}
			ObjectTree::growTree($this->level, $x, $y, $z, $random, $this->type);
		}
	}

	/**
	 * @param $x
	 * @param $z
	 *
	 * @return int
	 */
	private function getHighestWorkableBlock($x, $z){
		for($y = 127; $y >= 0; --$y){
			$b = $this->level->getBlockIdAt($x, $y, $z);
			if($b === Block::DIRT or $b === Block::GRASS or $b === Block::PODZOL){
				break;
			}elseif($b !== 0 and $b !== Block::SNOW_LAYER){
				return -1;
			}
		}

		return ++$y;
	}
}
