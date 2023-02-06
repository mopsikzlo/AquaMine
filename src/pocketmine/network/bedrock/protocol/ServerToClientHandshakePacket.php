<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\NetworkSession;

class ServerToClientHandshakePacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::SERVER_TO_CLIENT_HANDSHAKE_PACKET;

	/**
	 * @var string
	 * Server pubkey and token is contained in the JWT.
	 */
	public $jwt;

	public function canBeSentBeforeLogin() : bool{
		return true;
	}

	public function decodePayload(){
		$this->jwt = $this->getString();
	}

	public function encodePayload(){
		$this->putString($this->jwt);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleServerToClientHandshake($this);
	}
}
