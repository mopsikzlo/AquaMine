<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\NetworkSession;

class AutomationClientConnectPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::AUTOMATION_CLIENT_CONNECT_PACKET;

	/** @var string */
	public $serverUri;

	public function decodePayload(){
		$this->serverUri = $this->getString();
	}

	public function encodePayload(){
		$this->putString($this->serverUri);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleAutomationClientConnect($this);
	}
}
