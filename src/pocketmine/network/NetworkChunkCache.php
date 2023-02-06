<?php

declare(strict_types=1);

namespace pocketmine\network;

use pocketmine\Player;

interface NetworkChunkCache{

	/**
	 * Requests asynchronous preparation of the chunk at the given coordinates.
	 *
	 * @param Player $player
	 * @param int    $chunkX
	 * @param int    $chunkZ
	 */
	public function request(Player $player, int $chunkX, int $chunkZ) : void;

	/**
	 * @param Player $player
	 * @param int    $chunkX
	 * @param int    $chunkZ
	 */
	public function unregister(Player $player, int $chunkX, int $chunkZ) : void;

	/**
	 * Returns the number of bytes occupied by the cache data in this cache.
	 *
	 * @return int
	 */
	public function calculateCacheSize() : int;
}