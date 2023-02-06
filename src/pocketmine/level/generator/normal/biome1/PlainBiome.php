<?php

declare(strict_types=1);

namespace pocketmine\level\generator\normal\biome;

use pocketmine\level\generator\populator\StoneGround;
use pocketmine\level\generator\populator\Mushroom;
use pocketmine\level\generator\populator\TallGrass;
use pocketmine\level\generator\populator\RosePlant;
use pocketmine\level\generator\populator\PionPlant;
use pocketmine\level\generator\populator\SirenPlant;
use pocketmine\level\generator\populator\SunflowerPlant;
use pocketmine\level\generator\populator\MelonPlant;
use pocketmine\block\Block;
use pocketmine\block\Flower as FlowerBlock;
use pocketmine\level\generator\populator\Flower;

class PlainBiome extends GrassyBiome{

	public function __construct(){
		parent::__construct();
    
      $ground = new StoneGround();
    	$sun = new SunflowerPlant();
    	$melon = new MelonPlant();
	  	$RosePlant = new RosePlant();
   	$PionPlant = new PionPlant();
    	$SirenPlant = new SirenPlant();
	  	$tallGrass = new TallGrass();
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

      $this->addPopulator($ground);
		$this->addPopulator($RosePlant);
		$this->addPopulator($PionPlant);
		$this->addPopulator($SirenPlant);
      $this->addPopulator($sun);
		$this->addPopulator($mushroom);
		$this->addPopulator($tallGrass);
		$this->addPopulator($flower);
   	$this->addPopulator($melon);

		$this->setElevation(65, 81);

		$this->temperature = 1.0;
		$this->rainfall = 0.4;
	}

	public function getName() : string{
		return "Plains";
	}
}
