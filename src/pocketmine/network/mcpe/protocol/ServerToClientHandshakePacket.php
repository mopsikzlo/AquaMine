<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\NetworkSession;

class ServerToClientHandshakePacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::SERVER_TO_CLIENT_HANDSHAKE_PACKET;

	public $publicKey;
	public $serverToken;

	public function canBeSentBeforeLogin() : bool{
		return true;
	}

	public function decodePayload(){

	}

	public function encodePayload(){
		$this->putString($this->publicKey);
		$this->putString($this->serverToken);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleServerToClientHandshake($this);
	}
}