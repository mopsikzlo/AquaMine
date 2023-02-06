<?php

declare(strict_types = 1);

namespace pocketmine\level\generator\normal\biome;

use pocketmine\level\generator\populator\TallGrass;

class IcePlainsBiome extends SnowyBiome {

	/**
	 * IcePlainsBiome constructor.
	 */
	public function __construct(){
		parent::__construct();

		$tallGrass = new TallGrass();
		$tallGrass->setBaseAmount(5);

		$this->addPopulator($tallGrass);

		$this->setElevation(63, 74);

		$this->temperature = 0.05;
		$this->rainfall = 0.8;
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Ice Plains";
	}
}