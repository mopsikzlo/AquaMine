<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\NetworkSession;

class SubClientLoginPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::SUB_CLIENT_LOGIN_PACKET;

	/** @var string */
	public $connectionRequestData;

	public function decodePayload(){
		$this->connectionRequestData = $this->getString();
	}

	public function encodePayload(){
		$this->putString($this->connectionRequestData);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleSubClientLogin($this);
	}
}
