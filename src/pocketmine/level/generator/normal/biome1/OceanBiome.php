<?php

declare(strict_types=1);

namespace pocketmine\level\generator\normal\biome;

use pocketmine\level\generator\populator\Mushroom;
use pocketmine\level\generator\populator\SugarCane;
use pocketmine\level\generator\populator\SwampGrass;
use pocketmine\level\generator\populator\DirtCover;
use pocketmine\level\generator\populator\CarrotPlant;
use pocketmine\block\Sapling;
use pocketmine\block\Block;
use pocketmine\level\generator\populator\Tree;
use pocketmine\block\Flower as FlowerBlock;
use pocketmine\level\generator\populator\Flower;

class OceanBiome extends WateryBiome{

	public function __construct(){
		parent::__construct();

    $dirt = new DirtCover();
   $dirt->setBaseAmount(20);
    $carrot = new CarrotPlant();
     $carrot->setBaseAmount(1);
     $tree = new Tree(Sapling::OAK);
		$tree->setBaseAmount(1);
		$sugarcane = new SugarCane();
		$sugarcane->setBaseAmount(10);
		$tallGrass = new SwampGrass();
		$tallGrass->setBaseAmount(150);
		$mushroom = new Mushroom();
    	$flower = new Flower();
		$flower->setBaseAmount(5);
		$flower->addType([Block::DANDELION, 0]);
		$flower->addType([Block::RED_FLOWER, FlowerBlock::TYPE_POPPY]);
		$flower->addType([Block::RED_FLOWER, FlowerBlock::TYPE_AZURE_BLUET]);
		$flower->addType([Block::RED_FLOWER, FlowerBlock::TYPE_RED_TULIP]);
		$flower->addType([Block::RED_FLOWER, FlowerBlock::TYPE_ORANGE_TULIP]);
		$flower->addType([Block::RED_FLOWER, FlowerBlock::TYPE_WHITE_TULIP]);
		$flower->addType([Block::RED_FLOWER, FlowerBlock::TYPE_PINK_TULIP]);
		$flower->addType([Block::RED_FLOWER, FlowerBlock::TYPE_OXEYE_DAISY]);

        $this->addPopulator($dirt);
		$this->addPopulator($mushroom);
		$this->addPopulator($sugarcane);
        $this->addPopulator($carrot);
		$this->addPopulator($tallGrass);
        $this->addPopulator($tree);
        $this->addPopulator($flower);

		$this->setElevation(46, 72);

		$this->temperature = 1.0;
		$this->rainfall = 0.4;
	}

	public function getName() : string{
		return "Ocean";
	}
}
