<?php

declare(strict_types=1);

namespace pocketmine\level\generator\normal\biome;


class SmallMountainsBiome extends MountainsBiome{

	public function __construct(){
		parent::__construct();

		$this->setElevation(63, 97);
	}

	public function getName() : string{
		return "Small Mountains";
	}
}