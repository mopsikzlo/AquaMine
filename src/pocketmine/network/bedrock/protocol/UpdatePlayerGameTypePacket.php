<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\NetworkSession;

class UpdatePlayerGameTypePacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::UPDATE_PLAYER_GAME_TYPE_PACKET;

	/** @var int */
	public $gameType;
	/** @var int */
	public $playerUniqueId;

	public function decodePayload(){
		$this->gameType = $this->getVarInt();
		$this->playerUniqueId = $this->getActorUniqueId();
	}

	public function encodePayload(){
		$this->putVarInt($this->gameType);
		$this->putActorUniqueId($this->playerUniqueId);
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleUpdatePlayerGameType($this);
	}
}