<?php

namespace pocketmine\level\generator\populator;

use pocketmine\block\Block;
use pocketmine\block\Flower as FlowerBlock;
use pocketmine\level\loadchunk\ChunkManager;
use pocketmine\utils\Random;

class Flower extends Populator {
	/** @var ChunkManager */
	private $level;
	private $randomAmount;
	private $baseAmount = 8;

	private $flowerTypes = [];

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
	 * @param $type
	 */
	public function addType($type){
		$this->flowerTypes[] = $type;
	}

	/**
	 * @return array
	 */
	public function getTypes(){
		return $this->flowerTypes;
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
		$amount = $random->nextRange(0, $this->randomAmount + 1) + $this->baseAmount;

		if(count($this->flowerTypes) === 0){
			$this->addType([Block::DANDELION, 0]);
			$this->addType([Block::RED_FLOWER, FlowerBlock::TYPE_POPPY]);
		}

		$endNum = count($this->flowerTypes) - 1;

		for($i = 0; $i < $amount; ++$i){
			$x = $random->nextRange($chunkX * 16, $chunkX * 16 + 15);
			$z = $random->nextRange($chunkZ * 16, $chunkZ * 16 + 15);
			$y = $this->getHighestWorkableBlock($x, $z);
			if($y !== -1 and $this->canFlowerStay($x, $y, $z)){
				$type = mt_rand(0, $endNum);
				$this->level->setBlockIdAt($x, $y, $z, $this->flowerTypes[$type][0]);
				$this->level->setBlockDataAt($x, $y, $z, $this->flowerTypes[$type][1]);
			}
		}
	}

	/**
	 * @param $x
	 * @param $y
	 * @param $z
	 *
	 * @return bool
	 */
	private function canFlowerStay($x, $y, $z){
		$b = $this->level->getBlockIdAt($x, $y, $z);
		return ($b === Block::AIR or $b === Block::SNOW_LAYER) and $this->level->getBlockIdAt($x, $y - 1, $z) === Block::GRASS;
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
			if($b !== Block::AIR and $b !== Block::LEAVES and $b !== Block::LEAVES2 and $b !== Block::SNOW_LAYER){
				break;
			}
		}
		return $y === 0 ? -1 : ++$y;
	}
}