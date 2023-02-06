<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\NetworkSession;

class RequestChunkRadiusPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::REQUEST_CHUNK_RADIUS_PACKET;

	public $radius;

	public function decodePayload(){
		$this->radius = $this->getVarInt();
	}

	public function encodePayload(){
		$this->putVarInt($this->radius);
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleRequestChunkRadius($this);
	}

}
