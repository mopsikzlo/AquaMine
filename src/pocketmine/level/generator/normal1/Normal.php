<?php

/*
 *
 *  _                       _           _ __  __ _
 * (_)                     (_)         | |  \/  (_)
 *  _ _ __ ___   __ _  __ _ _  ___ __ _| | \  / |_ _ __   ___
 * | | '_ ` _ \ / _` |/ _` | |/ __/ _` | | |\/| | | '_ \ / _ \
 * | | | | | | | (_| | (_| | | (_| (_| | | |  | | | | | |  __/
 * |_|_| |_| |_|\__,_|\__, |_|\___\__,_|_|_|  |_|_|_| |_|\___|
 *                     __/ |
 *                    |___/
 *
 * This program is a third party build by ImagicalMine.
 *
 * PocketMine is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author ImagicalMine Team
 * @link http://forums.imagicalcorp.ml/
 *
 *
*/

namespace pocketmine\level\generator\normal;

use pocketmine\block\Block;
use pocketmine\block\CoalOre;
use pocketmine\block\DiamondOre;
use pocketmine\block\Dirt;
use pocketmine\block\GoldOre;
use pocketmine\block\Gravel;
use pocketmine\block\IronOre;
use pocketmine\block\LapisOre;
use pocketmine\block\RedstoneOre;
use pocketmine\block\Stone;
use pocketmine\level\ChunkManager;
use pocketmine\level\generator\biome\Biome;
use pocketmine\level\generator\biome\BiomeSelector;
use pocketmine\level\generator\Generator;
use pocketmine\level\generator\noise\Simplex;
use pocketmine\level\generator\object\OreType;
use pocketmine\level\generator\populator\Cave;
use pocketmine\level\generator\populator\GroundCover;
use pocketmine\level\generator\populator\Ore;
use pocketmine\level\generator\populator\Populator;
use pocketmine\level\Level;
use pocketmine\math\Vector3 as Vector3;
use pocketmine\utils\Random;

class Normal extends Generator {
	const NAME = "Normal";

	/** @var Populator[] */
	protected $populators = [];
	/** @var ChunkManager */
	protected $level;
	/** @var Random */
	protected $random;
	protected $waterHeight = 62;
	protected $bedrockDepth = 5;

	/** @var Populator[] */
	protected $generationPopulators = [];
	/** @var Simplex */
	protected $noiseBase;

	/** @var BiomeSelector */
	protected $selector;

	private $noiseSeaFloor;
	/** @var Simplex */
	private $noiseLand;
	/** @var Simplex */
	private $noiseMountains;
	/** @var Simplex */
	private $noiseBaseGround;
	/** @var Simplex */
	private $noiseRiver;

	private $heightOffset;

	private $seaHeight = 62;
	private $seaFloorHeight = 48;
	private $beathStartHeight = 60;
	private $beathStopHeight = 64;
	private $seaFloorGenerateRange = 5;
	private $landHeightRange = 20; // 18
	private $mountainHeight = 15; // 13
	private $basegroundHeight = 3;

	private static $GAUSSIAN_KERNEL = null;
	private static $SMOOTH_SIZE = 2;

	public function __construct(array $options = []){
		if(self::$GAUSSIAN_KERNEL === null){
			self::generateKernel();
		}
	}

	private static function generateKernel(){
		self::$GAUSSIAN_KERNEL = [];

		$bellSize = 1 / self::$SMOOTH_SIZE;
		$bellHeight = 2 * self::$SMOOTH_SIZE;

		for($sx = -self::$SMOOTH_SIZE; $sx <= self::$SMOOTH_SIZE; ++$sx){
			self::$GAUSSIAN_KERNEL[$sx + self::$SMOOTH_SIZE] = [];

			for($sz = -self::$SMOOTH_SIZE; $sz <= self::$SMOOTH_SIZE; ++$sz){
				$bx = $bellSize * $sx;
				$bz = $bellSize * $sz;
				self::$GAUSSIAN_KERNEL[$sx + self::$SMOOTH_SIZE][$sz + self::$SMOOTH_SIZE] = $bellHeight * exp(-($bx * $bx + $bz * $bz) / 4);
			}
		}
	}

	public function getName() : string{
		return self::NAME;
	}

	public function getWaterHeight() : int{
		return $this->waterHeight;
	}

	public function getSettings() : array{
		return [];
	}

	public function pickBiome($x, $z){
		$hash = $x * 2345803 ^ $z * 9236449 ^ $this->level->getSeed();
		$hash *= $hash + 223;
		$xNoise = $hash >> 20 & 3;
		$zNoise = $hash >> 22 & 3;
		if ($xNoise == 3) {
			$xNoise = 1;
		}
		if($zNoise == 3) {
			$zNoise = 1;
		}

		return $this->selector->pickBiome($x + $xNoise - 1, $z + $zNoise - 1);
	}

	public function init(ChunkManager $level, Random $random){
		
		$this->level = $level;
		$this->random = $random;
		$this->random->setSeed($this->level->getSeed());
		$this->noiseSeaFloor = new Simplex($this->random, 1, 1 / 8, 1 / 64);
		$this->noiseLand = new Simplex($this->random, 2, 1 / 8, 1 / 512);
		$this->noiseMountains = new Simplex($this->random, 4, 1, 1 / 500);
		$this->noiseBaseGround = new Simplex($this->random, 4, 1 / 4, 1 / 64);
		$this->noiseRiver = new Simplex($this->random, 2, 1, 1 / 512);
		$this->random->setSeed($this->level->getSeed());
		//$this->selector = new BiomeSelector($this->random, Biome::getBiome(Biome::OCEAN));

		$this->heightOffset = $random->nextRange(-5, 3);
		$this->selector = new BiomeSelector($this->random, function($temperature, $rainfall){
			if($rainfall < 0.25){
				if($temperature < 0.7){
					return Biome::OCEAN;
				}elseif($temperature < 0.85){
					return Biome::SAVANNA;
				}else{
					return Biome::SWAMP;
				}
				
			}elseif($rainfall < 0.60){
				if($temperature < 0.25){
					return Biome::ICE_PLAINS;
				}elseif($temperature < 0.75){
					return Biome::PLAINS;
				}else{
					return Biome::DESERT;
                }
			}elseif($rainfall < 0.80){
				if($temperature < 0.25){
					return Biome::TAIGA;
				}elseif($temperature < 0.75){
					return Biome::FOREST;
				}else{
					return Biome::BEACH;
				}
			}else{
				if($temperature < 0.25){
					return Biome::MOUNTAINS;
				}elseif($temperature < 0.70){
					return Biome::SMALL_MOUNTAINS;
				}else{
					return Biome::RIVER;
				}
			}
		}, Biome::getBiome(Biome::FOREST));

		$this->selector->addBiome(Biome::getBiome(Biome::FOREST));
		$this->selector->addBiome(Biome::getBiome(Biome::DESERT));
		$this->selector->addBiome(Biome::getBiome(Biome::ICE_PLAINS));
		$this->selector->addBiome(Biome::getBiome(Biome::SWAMP));
		$this->selector->addBiome(Biome::getBiome(Biome::RIVER));
		$this->selector->addBiome(Biome::getBiome(Biome::MOUNTAINS));
		$this->selector->addBiome(Biome::getBiome(Biome::SMALL_MOUNTAINS));
		$this->selector->addBiome(Biome::getBiome(Biome::BIRCH_FOREST));
		$this->selector->addBiome(Biome::getBiome(Biome::BEACH));
		$this->selector->addBiome(Biome::getBiome(Biome::PLAINS));
		$this->selector->addBiome(Biome::getBiome(Biome::TAIGA));
		$this->selector->addBiome(Biome::getBiome(Biome::SAVANNA));

		$this->selector->recalculate();

		$cover = new GroundCover();
		$this->generationPopulators[] = $cover;

		$cave = new Cave();
		$this->populators[] = $cave;

		$ores = new Ore();
		$ores->setOreTypes([
			new OreType(new CoalOre(), 20, 16, 0, 128),
			new OreType(New IronOre(), 20, 8, 0, 64),
			new OreType(new RedstoneOre(), 8, 7, 0, 16),
			new OreType(new LapisOre(), 1, 6, 0, 32),
			new OreType(new GoldOre(), 2, 8, 0, 32),
			new OreType(new DiamondOre(), 1, 7, 0, 16),
			new OreType(new Dirt(), 20, 32, 0, 128),
			new OreType(new Stone(Stone::GRANITE), 20, 32, 0, 128),
			new OreType(new Stone(Stone::DIORITE), 20, 32, 0, 128),
			new OreType(new Stone(Stone::ANDESITE), 20, 32, 0, 128),
			new OreType(new Gravel(), 10, 16, 0, 128)
		]);
		$this->populators[] = $ores;
	}

	public function generateChunk($chunkX, $chunkZ){
		$this->random->setSeed(0xdeadbeef ^ ($chunkX << 8) ^ $chunkZ ^ $this->level->getSeed());

		$seaFloorNoise = Generator::getFastNoise2D($this->noiseSeaFloor, 16, 16, 4, $chunkX * 16, 0, $chunkZ * 16);
		$landNoise = Generator::getFastNoise2D($this->noiseLand, 16, 16, 4, $chunkX * 16, 0, $chunkZ * 16);
		$mountainNoise = Generator::getFastNoise2D($this->noiseMountains, 16, 16, 4, $chunkX * 16, 0, $chunkZ * 16);
		$baseNoise = Generator::getFastNoise2D($this->noiseBaseGround, 16, 16, 4, $chunkX * 16, 0, $chunkZ * 16);
		$riverNoise = Generator::getFastNoise2D($this->noiseRiver, 16, 16, 4, $chunkX * 16, 0, $chunkZ * 16);

		$chunk = $this->level->getChunk($chunkX, $chunkZ);

		$biomeCache = [];

		for($genx = 0; $genx < 16; $genx++){
			for($genz = 0; $genz < 16; $genz++){
				$canBaseGround = false;
				$canRiver = true;

				//using a quadratic function which smooth the world
				//y = (2.956x)^2 - 0.6,  (0 <= x <= 2)
				$landHeightNoise = $landNoise[$genx][$genz] + 1;
				$landHeightNoise *= 2.956;
				$landHeightNoise = $landHeightNoise * $landHeightNoise;
				$landHeightNoise = $landHeightNoise - 0.6;
				$landHeightNoise = $landHeightNoise > 0 ? $landHeightNoise : 0;

				//generate mountains
				$mountainHeightGenerate = $mountainNoise[$genx][$genz] - 0.2;
				$mountainHeightGenerate = $mountainHeightGenerate > 0 ? $mountainHeightGenerate : 0;
				$mountainGenerate = (int) ($this->mountainHeight * $mountainHeightGenerate);

				$landHeightGenerate = (int) ($this->landHeightRange * $landHeightNoise);
				if($landHeightGenerate > $this->landHeightRange){
					if($landHeightGenerate > $this->landHeightRange){
						$canBaseGround = true;
					}
					$landHeightGenerate = $this->landHeightRange;
				}

				$genyHeight = $this->seaFloorHeight + $landHeightGenerate;
				$genyHeight += $mountainGenerate;

				//prepare for generate ocean, desert, and land
				/*if($genyHeight < $this->beathStartHeight){
					if($genyHeight < $this->beathStartHeight - 5){
						$genyHeight += (int) ($this->seaFloorGenerateRange * $seaFloorNoise[$genx][$genz]);
					}
					$biome = Biome::getBiome(Biome::OCEAN);
					if($genyHeight < $this->seaFloorHeight - $this->seaFloorGenerateRange){
						$genyHeight = $this->seaFloorHeight;
					}
					$canRiver = false;*/
				if($genyHeight <= $this->beathStopHeight && $genyHeight >= $this->beathStartHeight){
					$biome = Biome::getBiome(Biome::FOREST);
				}else{
					$biome = $this->pickBiome($chunkX * 16 + $genx, $chunkZ * 16 + $genz);
					if($canBaseGround){
						$baseGroundHeight = (int) ($this->landHeightRange * $landHeightNoise) - $this->landHeightRange;
						$baseGroundHeight2 = (int) ($this->basegroundHeight * ($baseNoise[$genx][$genz] + 1));
						if($baseGroundHeight2 > $baseGroundHeight) $baseGroundHeight2 = $baseGroundHeight;
						if($baseGroundHeight2 > $mountainGenerate)
							$baseGroundHeight2 = $baseGroundHeight2 - $mountainGenerate;
						else $baseGroundHeight2 = 0;
						$genyHeight += $baseGroundHeight2;
					}
				}
				if($canRiver && $genyHeight <= $this->seaHeight - 5){
					$canRiver = false;
				}
				//generate river
				if($canRiver){
					$riverGenerate = $riverNoise[$genx][$genz];
					if($riverGenerate > -0.25 && $riverGenerate < 0.25){
						$riverGenerate = $riverGenerate > 0 ? $riverGenerate : -$riverGenerate;
						$riverGenerate = 0.25 - $riverGenerate;
						//y=x^2 * 4 - 0.0000001
						$riverGenerate = $riverGenerate * $riverGenerate * 4;
						//smooth again
						$riverGenerate = $riverGenerate - 0.0000001;
						$riverGenerate = $riverGenerate > 0 ? $riverGenerate : 0;
						$genyHeight -= $riverGenerate * 64;
						if($genyHeight < $this->seaHeight){
							$biome = Biome::getBiome(Biome::RIVER);
							//to generate river floor
							if($genyHeight <= $this->seaHeight - 8){
								$genyHeight1 = $this->seaHeight - 9 + (int) ($this->basegroundHeight * ($baseNoise[$genx][$genz] + 1));
								$genyHeight2 = $genyHeight < $this->seaHeight - 7 ? $this->seaHeight - 7 : $genyHeight;
								$genyHeight = $genyHeight1 > $genyHeight2 ? $genyHeight1 : $genyHeight2;
							}
						}
					}
				}
				$chunk->setBiomeId($genx, $genz, $biome->getId());
				//generating
				$generateHeight = $genyHeight > $this->seaHeight ? $genyHeight : $this->seaHeight;
				for($geny = 0; $geny <= $generateHeight; $geny++){
					if($geny <= $this->bedrockDepth && ($geny == 0 or $this->random->nextRange(1, 5) == 1)){
						$chunk->setBlockId($genx, $geny, $genz, Block::BEDROCK);
					}elseif($geny > $genyHeight){
						if(($biome->getId() == Biome::ICE_PLAINS or $biome->getId() == Biome::TAIGA) and $geny == $this->seaHeight){
							$chunk->setBlockId($genx, $geny, $genz, Block::ICE);
						}else{
							$chunk->setBlockId($genx, $geny, $genz, Block::STILL_WATER);
						}
					}else{
						$chunk->setBlockId($genx, $geny, $genz, Block::STONE);
					}
				}
			}
		}

		foreach($this->generationPopulators as $populator){
			$populator->populate($this->level, $chunkX, $chunkZ, $this->random);
		}
	}

	public function populateChunk($chunkX, $chunkZ){
		$this->random->setSeed(0xdeadbeef ^ ($chunkX << 8) ^ $chunkZ ^ $this->level->getSeed());
		foreach($this->populators as $populator){
			$populator->populate($this->level, $chunkX, $chunkZ, $this->random);
		}

		$chunk = $this->level->getChunk($chunkX, $chunkZ);
		$biome = Biome::getBiome($chunk->getBiomeId(7, 7));
		$biome->populateChunk($this->level, $chunkX, $chunkZ, $this->random);
	}

	public function getSpawn() : Vector3{
		return new Vector3(127.5, $this->level->getMaxY(), 127.5);
	}

}