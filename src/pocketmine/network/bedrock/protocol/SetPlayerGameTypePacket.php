<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\NetworkSession;

class SetPlayerGameTypePacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::SET_PLAYER_GAME_TYPE_PACKET;

	/** @var int */
	public $gamemode;

	public function decodePayload(){
		$this->gamemode = $this->getVarInt();
	}

	public function encodePayload(){
		$this->putVarInt($this->gamemode);
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleSetPlayerGameType($this);
	}
}
