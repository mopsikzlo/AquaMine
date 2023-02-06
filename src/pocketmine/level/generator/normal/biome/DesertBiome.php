<?php

declare(strict_types = 1);

namespace pocketmine\level\generator\normal\biome;


class DesertBiome extends SandyBiome {

	/**
	 * DesertBiome constructor.
	 */
	public function __construct(){
		parent::__construct();
		$this->setElevation(63, 74);

		$this->temperature = 2;
		$this->rainfall = 0;
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Desert";
	}
}