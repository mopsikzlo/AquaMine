<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\NetworkSession;

class DebugInfoPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::DEBUG_INFO_PACKET;

	/** @var int */
	public $playerUniqueId;
	/** @var string */
	public $data;

	public function decodePayload(){
		$this->playerUniqueId = $this->getActorUniqueId();
		$this->data = $this->getString();
	}

	public function encodePayload(){
		$this->putActorUniqueId($this->playerUniqueId);
		$this->putString($this->data);
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleDebugInfo($this);
	}
}