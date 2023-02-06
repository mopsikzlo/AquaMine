<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\NetworkSession;

class ServerSettingsRequestPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::SERVER_SETTINGS_REQUEST_PACKET;

	public function decodePayload(){
		//No payload
	}

	public function encodePayload(){
		//No payload
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleServerSettingsRequest($this);
	}
}
