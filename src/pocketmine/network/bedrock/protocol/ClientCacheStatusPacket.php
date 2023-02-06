<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\NetworkSession;

class ClientCacheStatusPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::CLIENT_CACHE_STATUS_PACKET;

	/** @var bool */
	public $enabled;

	public function decodePayload(){
		$this->enabled = $this->getBool();
	}

	public function encodePayload(){
		$this->putBool($this->enabled);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleClientCacheStatus($this);
	}
}
