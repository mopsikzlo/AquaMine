<?php

declare(strict_types=1);

namespace pocketmine\level\generator;

use pocketmine\level\loadchunk\ChunkManager;
use pocketmine\level\format\Chunk;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;

use function count;
use function explode;
use function preg_match_all;
use function str_replace;

class VoidGenerator extends Generator{
	/** @var ChunkManager */
	private $level;
	/** @var ?Chunk */
	private $chunk;

	public function getSettings() : array{
		return [];
	}

	public function getName() : string{
		return "void";
	}

	public function __construct(array $options = []){

	}

	public function init(ChunkManager $level, Random $random){
		$this->level = $level;
	}

	public function generateChunk(int $chunkX, int $chunkZ){
		if($this->chunk === null){
			$this->chunk = new Chunk($chunkX, $chunkZ);
			$this->chunk->setGenerated();
		}
		$chunk = clone $this->chunk;
		$chunk->setX($chunkX);
		$chunk->setZ($chunkZ);
		$this->level->setChunk($chunkX, $chunkZ, $chunk);
	}

	public function populateChunk(int $chunkX, int $chunkZ){

	}

	public function getSpawn() : Vector3{
		return new Vector3(0, 128, 0);
	}
}