<?php

declare(strict_types=1);

namespace pocketmine\level\generator;

use pocketmine\block\Block;
use pocketmine\level\generator\biome\Biome;
use pocketmine\level\Level;
use pocketmine\level\SimpleChunkManager;
use pocketmine\scheduler\AsyncTask;
use pocketmine\utils\Random;

use function get_class;
use function igbinary_serialize;
use function igbinary_unserialize;

class GeneratorRegisterTask extends AsyncTask{

	public $generator;
	public $settings;
	public $seed;
	public $levelId;
	public $worldHeight = Level::Y_MAX;

	public function __construct(Level $level, string $generatorClass, array $generatorSettings = []){
		$this->generator = $generatorClass;
		$this->settings = igbinary_serialize($generatorSettings);
		$this->seed = $level->getSeed();
		$this->levelId = $level->getId();
		$this->worldHeight = $level->getWorldHeight();
	}

	public function onRun(){
		/** @var Generator $generator */
		$generator = $this->generator;

		if($generator !== VoidGenerator::class){
			Block::init();
			Biome::init();
		}
		$manager = new SimpleChunkManager($this->seed, $this->worldHeight);
		$this->saveToThreadStore("generation.level{$this->levelId}.manager", $manager);

		$generator = new $generator(igbinary_unserialize($this->settings));
		$generator->init($manager, new Random($manager->getSeed()));
		$this->saveToThreadStore("generation.level{$this->levelId}.generator", $generator);
	}
}
