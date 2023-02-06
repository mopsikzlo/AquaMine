<?php

declare(strict_types=1);

namespace pocketmine\level\generator\normal\biome;

use pocketmine\level\generator\populator\DirtCover;
use pocketmine\level\generator\populator\Mushroom;
use pocketmine\level\generator\populator\MountainBush;
use pocketmine\level\generator\populator\WheatStack;
use pocketmine\level\generator\populator\TallGrass;
use pocketmine\level\generator\populator\Tree;
use pocketmine\block\Sapling;
use pocketmine\block\Block;
use pocketmine\level\generator\populator\BeetrootPlant;

class MountainsBiome extends GrassyBiome {

	public function __construct() {
		parent::__construct();
     $BeetrootPlant = new BeetrootPlant();
     $BeetrootPlant->setBaseAmount(1);
 		$dirt = new DirtCover();
 		$dirt->setBaseAmount(10);
 		$wheat = new WheatStack();
 		$wheat->setBaseAmount(1);		
 		$bush = new MountainBush();
 		$bush->setBaseAmount(4);		
		$trees = new Tree(Sapling::DARK_OAK);
		$this->addPopulator($dirt);
		$this->addPopulator($bush);
		$this->addPopulator($wheat);				
     $this->addPopulator($BeetrootPlant);
		$this->addPopulator($trees);

		$tallGrass = new TallGrass();
		$tallGrass->setBaseAmount(150);

		$this->addPopulator($tallGrass);

		$this->setElevation(63, 127);

		$this->temperature = 0.7;
		$this->rainfall = 0.5;
	}

	public function getName() :string {
		return "Mountains";
	}
}