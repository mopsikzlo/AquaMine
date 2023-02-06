<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\adapter;

use pocketmine\network\bedrock\protocol\DataPacket;

interface ProtocolAdapter{

	/**
	 * @param string $buf
	 *
	 * @return DataPacket|null
	 */
	public function processClientToServer(string $buf) : ?DataPacket;

	/**
	 * @param DataPacket $packet
	 *
	 * @return DataPacket|null
	 */
	public function processServerToClient(DataPacket $packet) : ?DataPacket;

	/**
	 * @param int $runtimeId
	 *
	 * @return int
	 */
	public function translateBlockId(int $runtimeId) : int;

	/**
	 * @return int
	 */
	public function getChunkProtocol() : int;

	/**
	 * @return int
	 */
	public function getProtocolVersion() : int;
}