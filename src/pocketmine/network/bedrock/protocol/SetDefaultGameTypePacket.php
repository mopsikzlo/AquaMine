<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\NetworkSession;

class SetDefaultGameTypePacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::SET_DEFAULT_GAME_TYPE_PACKET;

	/** @var int */
	public $gamemode;

	public function decodePayload(){
		$this->gamemode = $this->getVarInt();
	}

	public function encodePayload(){
		$this->putUnsignedVarInt($this->gamemode);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleSetDefaultGameType($this);
	}
}
