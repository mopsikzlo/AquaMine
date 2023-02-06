<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\bedrock\protocol\types\ChunkCacheBlob;
use pocketmine\network\NetworkSession;

class ClientCacheMissResponsePacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::CLIENT_CACHE_MISS_RESPONSE_PACKET;

	/** @var ChunkCacheBlob[] */
	public $blobs = [];

	public function decodePayload(){
		for($i = 0, $count = $this->getUnsignedVarInt(); $i < $count; ++$i){
			$hash = $this->getLLong();
			$payload = $this->getString();
			$this->blobs[] = new ChunkCacheBlob($hash, $payload);
		}
	}

	public function encodePayload(){
		$this->putUnsignedVarInt(count($this->blobs));
		foreach($this->blobs as $blob){
			$this->putLLong($blob->hash);
			$this->putString($blob->payload);
		}
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleClientCacheMissResponse($this);
	}
}
