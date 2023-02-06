<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\NetworkSession;

class ClientToServerHandshakePacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::CLIENT_TO_SERVER_HANDSHAKE_PACKET;

	public function canBeSentBeforeLogin() : bool{
		return true;
	}

	public function decodePayload(){
		//No payload
	}

	public function encodePayload(){
		//No payload
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleClientToServerHandshake($this);
	}
}
