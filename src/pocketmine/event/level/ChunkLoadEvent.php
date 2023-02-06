<?php

declare(strict_types=1);


namespace pocketmine\event\level;

use pocketmine\level\Level;
use pocketmine\level\format\Chunk;

/**
 * Called when a Chunk is loaded
 */
class ChunkLoadEvent extends ChunkEvent{
	public static $handlerList = null;

	/** @var bool */
	private $newChunk;

	public function __construct(Level $level, Chunk $chunk, bool $newChunk){
		parent::__construct($level, $chunk);
		$this->newChunk = $newChunk;
	}

	/**
	 * @return bool
	 */
	public function isNewChunk() : bool{
		return $this->newChunk;
	}
}