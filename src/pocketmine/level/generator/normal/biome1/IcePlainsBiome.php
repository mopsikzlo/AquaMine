<?php

declare(strict_types=1);

namespace pocketmine\level\generator\normal\biome;

use pocketmine\block\Sapling;
use pocketmine\block\Block;
use pocketmine\level\generator\populator\Tree;
use pocketmine\level\generator\populator\TallGrass;
use pocketmine\level\generator\populator\IceCover;

class IcePlainsBiome extends SnowyBiome{

	public function __construct(){
		parent::__construct();

    $ice = new IceCover();
    $ice->setBaseAmount(15);
		$trees = new Tree(Sapling::SPRUCE);
		$trees->setBaseAmount(1);
		$tallGrass = new TallGrass();
		$tallGrass->setBaseAmount(25);

   $this->addPopulator($ice);
		$this->addPopulator($tallGrass);
    $this->addPopulator($trees);

		$this->setElevation(63, 74);

		$this->temperature = 0.05;
		$this->rainfall = 0.8;
	}

	public function getName() : string{
		return "Ice Plains";
	}
}