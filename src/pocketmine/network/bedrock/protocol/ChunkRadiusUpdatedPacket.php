<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\NetworkSession;

class ChunkRadiusUpdatedPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::CHUNK_RADIUS_UPDATED_PACKET;

	/** @var int */
	public $radius;

	public function decodePayload(){
		$this->radius = $this->getVarInt();
	}

	public function encodePayload(){
		$this->putVarInt($this->radius);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleChunkRadiusUpdated($this);
	}
}
