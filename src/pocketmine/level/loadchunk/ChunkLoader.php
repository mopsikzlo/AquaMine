<?php

declare(strict_types=1);

namespace pocketmine\level\loadchunk;

/**
 * If you want to keep chunks loaded, implement this interface and register it into Level. This will also tick chunks.
 *
 * @see Level::registerChunkLoader()
 * @see Level::unregisterChunkLoader()
 *
 * WARNING: When moving this object around in the world or destroying it,
 * be sure to unregister the loader from chunks you're not using, otherwise you'll leak memory.
 */
interface ChunkLoader{

	/**
	 * Returns the ChunkLoader id.
	 * Call Level::generateChunkLoaderId($this) to generate and save it
	 *
	 * @return int
	 */
	public function getLoaderId() : int;

	/**
	 * Returns if the chunk loader is currently active
	 *
	 * @return bool
	 */
	public function isLoaderActive() : bool;

	/**
	 * @return Position
	 */
	public function getPosition();

	/**
	 * @return float
	 */
	public function getX();

	/**
	 * @return float
	 */
	public function getZ();

	/**
	 * @return Level
	 */
	public function getLevel();
}