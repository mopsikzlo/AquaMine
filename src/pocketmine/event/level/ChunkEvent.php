<?php

declare(strict_types=1);


namespace pocketmine\event\level;

use pocketmine\level\Level;
use pocketmine\level\format\Chunk;

/**
 * Chunk-related events
 */
abstract class ChunkEvent extends LevelEvent{
	/** @var Chunk */
	private $chunk;

	/**
	 * @param Level $level
	 * @param Chunk $chunk
	 */
	public function __construct(Level $level, Chunk $chunk){
		parent::__construct($level);
		$this->chunk = $chunk;
	}

	/**
	 * @return Chunk
	 */
	public function getChunk() : Chunk{
		return $this->chunk;
	}
}