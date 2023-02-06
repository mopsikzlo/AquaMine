<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
*/

namespace pocketmine\level\generator\normal\biome;

use pocketmine\block\{Sapling, Block, Flower as FlowerBlock};
use pocketmine\level\generator\populator\{Tree, TallGrass, Flower, Pumpkin, RedMushroom, BrownMushroom, Bushes, Sugarcane};

class ForestBiome extends GrassyBiome{

	const TYPE_NORMAL = 0;
	const TYPE_BIRCH = 1;

	public $type;

	public function __construct($type = self::TYPE_NORMAL){
		parent::__construct();

		$this->type = $type;

		$trees = new Tree($type === self::TYPE_BIRCH ? Sapling::BIRCH : Sapling::OAK);
		$trees->setBaseAmount(15);

		$tallGrass = new TallGrass();
		$tallGrass->setBaseAmount(5);
		
		$sugarcane = new Sugarcane();
		$sugarcane->setBaseAmount(4);
		
		$flower = new Flower();
		$flower->setBaseAmount(2);
		$flower->addType([Block::DANDELION, 0]);
		$flower->addType([Block::RED_FLOWER, FlowerBlock::TYPE_POPPY]);
		$flower->addType([Block::RED_FLOWER, FlowerBlock::TYPE_AZURE_BLUET]);
		$flower->addType([Block::RED_FLOWER, FlowerBlock::TYPE_RED_TULIP]);
		$flower->addType([Block::RED_FLOWER, FlowerBlock::TYPE_ORANGE_TULIP]);
		$flower->addType([Block::RED_FLOWER, FlowerBlock::TYPE_WHITE_TULIP]);
		$flower->addType([Block::RED_FLOWER, FlowerBlock::TYPE_PINK_TULIP]);
		$flower->addType([Block::RED_FLOWER, FlowerBlock::TYPE_OXEYE_DAISY]);

		$pumpkin = new Pumpkin();
		$pumpkin->setBaseAmount(1);

		$redmushroom = new RedMushroom();
		$redmushroom->setBaseAmount(1);
		
		$brownmushroom = new BrownMushroom();
		$brownmushroom->setBaseAmount(1);
			
		$bushes = new Bushes();
		$bushes->setBaseAmount(1);	

		$this->addPopulator($trees);
		$this->addPopulator($tallGrass);
		$this->addPopulator($flower);
		$this->addPopulator($redmushroom);
		$this->addPopulator($pumpkin);
		$this->addPopulator($brownmushroom);
		$this->addPopulator($bushes);
		$this->addPopulator($sugarcane);
		
		$this->setElevation(55, 77);

		if($type === self::TYPE_BIRCH){
			$this->temperature = 0.6;
			$this->rainfall = 0.5;
		}else{
			$this->temperature = 0.7;
			$this->rainfall = 0.8;
		}
	}

	public function getName(){
		return $this->type === self::TYPE_BIRCH ? "Birch Forest" : "Forest";
	}
}