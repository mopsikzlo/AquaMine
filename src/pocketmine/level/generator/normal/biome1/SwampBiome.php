<?php

declare(strict_types=1);

namespace pocketmine\level\generator\normal\biome;

use pocketmine\block\Sapling;
use pocketmine\level\generator\populator\Tree;
use pocketmine\block\Block;
use pocketmine\block\Flower as FlowerBlock;
use pocketmine\level\generator\populator\Flower;
use pocketmine\level\generator\populator\LilyPad;
use pocketmine\level\generator\populator\Mushroom;
use pocketmine\level\generator\populator\SugarCane;
use pocketmine\level\generator\populator\SwampGrass;
use pocketmine\level\generator\populator\DirtCover;

class SwampBiome extends GrassyBiome{

	public function __construct(){
		parent::__construct();

   $dirt = new DirtCover();
   $dirt->setBaseAmount(15);

    $tree = new Tree(Sapling::OAK);
		$tree->setBaseAmount(1);

		$lilyPad = new LilyPad();
		$lilyPad->setBaseAmount(3);

		$tallGrass = new SwampGrass();
		$tallGrass->setBaseAmount(50);

		$mushroom = new Mushroom();
		$sugarCane = new SugarCane();
		$sugarCane->setBaseAmount(2);
		$sugarCane->setRandomAmount(15);

		$this->addPopulator($mushroom);
		$this->addPopulator($lilyPad);
		$this->addPopulator($tree);
		$this->addPopulator($tallGrass);
		$this->addPopulator($sugarCane);
		$this->setElevation(60, 67);

		$this->temperature = 0.8;
		$this->rainfall = 0.9;
	}

	public function getName() : string{
		return "Swamp";
	}
}