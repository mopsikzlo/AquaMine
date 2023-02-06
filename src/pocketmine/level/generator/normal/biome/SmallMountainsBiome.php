<?php

namespace pocketmine\level\generator\normal\biome;


class SmallMountainsBiome extends MountainsBiome {

	/**
	 * SmallMountainsBiome constructor.
	 */
	public function __construct(){
		parent::__construct();

		$this->setElevation(63, 97);
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Small Mountains";
	}
}