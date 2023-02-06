<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol\types;

class ChunkCacheBlob{
	/** @var int */
	public $hash;
	/** @var string */
	public $payload;

	/**
	 * ChunkCacheBlob constructor.
	 *
	 * @param int    $hash
	 * @param string $payload
	 */
	public function __construct(int $hash, string $payload){
		$this->hash = $hash;
		$this->payload = $payload;
	}
}