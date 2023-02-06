<?php

declare(strict_types = 1);

namespace pocketmine\level\generator\normal\biome;

use pocketmine\block\Sapling;
use pocketmine\level\generator\populator\TallGrass;
use pocketmine\level\generator\populator\Tree;

class ForestBiome extends GrassyBiome {

	const TYPE_NORMAL = 0;
	const TYPE_BIRCH = 1;

	public $type;

	/**
	 * ForestBiome constructor.
	 *
	 * @param int $type
	 */
	public function __construct($type = self::TYPE_NORMAL){
		parent::__construct();

		$this->type = $type;

		$trees = new Tree($type === self::TYPE_BIRCH ? Sapling::BIRCH : Sapling::OAK);
		$trees->setBaseAmount(5);
		$this->addPopulator($trees);

		$tallGrass = new TallGrass();
		$tallGrass->setBaseAmount(3);

		$this->addPopulator($tallGrass);

		$this->setElevation(63, 81);

		if($type === self::TYPE_BIRCH){
			$this->temperature = 0.6;
			$this->rainfall = 0.5;
		}else{
			$this->temperature = 0.7;
			$this->rainfall = 0.8;
		}
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return $this->type === self::TYPE_BIRCH ? "Birch Forest" : "Forest";
	}
}