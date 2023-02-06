<?php

declare(strict_types=1);

namespace pocketmine\level\generator\normal\biome;

use pocketmine\block\Sapling;
use pocketmine\block\Block;
use pocketmine\level\generator\populator\Mushroom;
use pocketmine\level\generator\populator\StoneGround;
use pocketmine\level\generator\populator\Tree;

class TaigaBiome extends SnowyBiome{

	public function __construct(){
		parent::__construct();

		$ground = new StoneGround();
		$this->addPopulator($ground);
		
		$trees = new Tree(Sapling::DARK_OAK);
		$trees->setBaseAmount(1);
		$this->addPopulator($trees);

		$mushroom = new Mushroom();
		$this->addPopulator($mushroom);

		$this->setElevation(63, 83);

		$this->temperature = 0.05;
		$this->rainfall = 0.8;

		$this->setGroundCover([
			Block::get(Block::STONE, 0),
			Block::get(Block::STONE, 0),
			Block::get(Block::STONE, 0),
			Block::get(Block::STONE, 0)
		]);
	}

	public function getName() : string{
		return "Taiga";
	}
}
