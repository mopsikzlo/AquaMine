<?php

declare(strict_types=1);

namespace pocketmine\level\generator\normal\biome;

use pocketmine\block\Block;
use pocketmine\level\generator\populator\DeadBush;
use pocketmine\level\generator\populator\Mushroom;
use pocketmine\level\generator\populator\SugarCane;
use pocketmine\level\generator\populator\Cactus;

class DesertBiome extends SandyBiome{

	public function __construct(){
		$cactus = new Cactus();
    $cactus->setBaseAmount(3);

		$deadBush = new DeadBush();
		$deadBush->setBaseAmount(1);
		$deadBush->setRandomAmount(4);

		$sugarCane = new SugarCane();
		$sugarCane->setRandomAmount(20);
		$sugarCane->setBaseAmount(3);

		$mushroom = new Mushroom();

        $this->addPopulator($cactus);
		$this->addPopulator($mushroom);
		$this->addPopulator($deadBush);
		$this->addPopulator($sugarCane);
		
		$this->setElevation(63, 74);

		$this->temperature = 2;
		$this->rainfall = 0;
		$this->setGroundCover([
			Block::get(Block::SAND, 0),
			Block::get(Block::SAND, 0),
			Block::get(Block::SAND, 0),
			Block::get(Block::SAND, 0),
			Block::get(Block::SANDSTONE, 0),
			Block::get(Block::SANDSTONE, 0),
			Block::get(Block::SANDSTONE, 0),
			Block::get(Block::SANDSTONE, 0),
			Block::get(Block::SANDSTONE, 0),
			Block::get(Block::SANDSTONE, 0),
			Block::get(Block::SANDSTONE, 0),
			Block::get(Block::SANDSTONE, 0),
			Block::get(Block::SANDSTONE, 0),
			Block::get(Block::SANDSTONE, 0),
			Block::get(Block::SANDSTONE, 0),
			Block::get(Block::SANDSTONE, 0),
			Block::get(Block::SANDSTONE, 0),
			Block::get(Block::SANDSTONE, 0),
			Block::get(Block::SANDSTONE, 0),
			Block::get(Block::SANDSTONE, 0),
			Block::get(Block::SANDSTONE, 0),
			Block::get(Block::SANDSTONE, 0),
			Block::get(Block::SANDSTONE, 0),
			Block::get(Block::SANDSTONE, 0),
			Block::get(Block::SANDSTONE, 0),
			Block::get(Block::SANDSTONE, 0),
			Block::get(Block::SANDSTONE, 0),
			Block::get(Block::SANDSTONE, 0),
			Block::get(Block::SANDSTONE, 0),
			Block::get(Block::SANDSTONE, 0),
			Block::get(Block::SANDSTONE, 0),
			Block::get(Block::SANDSTONE, 0),
			Block::get(Block::SANDSTONE, 0),
			Block::get(Block::SANDSTONE, 0),
			Block::get(Block::SANDSTONE, 0),
		]);
	}

	public function getName() : string{
		return "Desert";
	}
}