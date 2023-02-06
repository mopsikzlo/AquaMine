<?php

namespace pocketmine\level\generator\normal\biome;

use pocketmine\block\Block;
use pocketmine\block\Sapling;
use pocketmine\level\generator\populator\MossStone;
use pocketmine\level\generator\populator\Tree;
use pocketmine\level\generator\populator\TallGrass;

class TaigaBiome extends SnowyBiome {

	/**
	 * TaigaBiome constructor.
	 */
	public function __construct(){
		parent::__construct();

		$trees = new Tree(Sapling::SPRUCE);
		$trees->setBaseAmount(10);
		$this->addPopulator($trees);

		$mossStone = new MossStone();
		$mossStone->setBaseAmount(1);

		$tallGrass = new TallGrass();
		$tallGrass->setBaseAmount(1);

		$this->addPopulator($mossStone);
		$this->addPopulator($tallGrass);

		$this->setElevation(63, 81);

		$this->temperature = 0.05;
		$this->rainfall = 0.8;

		$this->setGroundCover([
			Block::get(Block::PODZOL, 0),
			Block::get(Block::DIRT, 0),
			Block::get(Block::DIRT, 0),
			Block::get(Block::DIRT, 0)
		]);
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Taiga";
	}
}
