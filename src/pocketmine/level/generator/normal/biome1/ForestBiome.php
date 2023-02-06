<?php

declare(strict_types=1);

namespace pocketmine\level\generator\normal\biome;

use pocketmine\block\Sapling;
use pocketmine\level\generator\populator\Mushroom;
use pocketmine\level\generator\populator\StoneCover;
use pocketmine\level\generator\populator\ForestBush;
use pocketmine\level\generator\populator\ForestGrass;
use pocketmine\level\generator\populator\Tree;
use pocketmine\level\generator\populator\WheatPlant;
use pocketmine\block\Block;
use pocketmine\block\Flower as FlowerBlock;
use pocketmine\level\generator\populator\Flower;

class ForestBiome extends GrassyBiome{

	const TYPE_NORMAL = 0;
	const TYPE_BIRCH = 1;

	public $type;

	public function __construct($type = self::TYPE_NORMAL){
		parent::__construct();

		$this->type = $type;
		$trees = new Tree($type === self::TYPE_BIRCH ? Sapling::BIRCH : Sapling::OAK);
		$trees->setBaseAmount(2);
		$this->addPopulator($trees);

    	$wheat = new WheatPlant();
  	   $wheat->setBaseAmount(1);
  		$ForestBush = new ForestBush();
   	$ForestBush->setBaseAmount(5);
  		$stone = new StoneCover();
   	$stone->setBaseAmount(10);
		$tallGrass = new ForestGrass();
		$tallGrass->setBaseAmount(150);
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

    $this->addPopulator($ForestBush);
    $this->addPopulator($stone);
    $this->addPopulator($wheat);
    $this->addPopulator($flower);

		$this->addPopulator($tallGrass);
		
		$mushroom = new Mushroom();
		$this->addPopulator($mushroom);

		$this->setElevation(63, 81);

		if($type === self::TYPE_BIRCH){
			$this->temperature = 0.7;
			$this->rainfall = 0.5;
		}else{
			$this->temperature = 0.7;
			$this->temperature = 0.8;
		}
	}

	public function getName() : string{
		return $this->type === self::TYPE_BIRCH ? "Birch Forest" : "Forest";
	}
}