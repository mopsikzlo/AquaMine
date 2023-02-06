<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\NetworkSession;

class SetHealthPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::SET_HEALTH_PACKET;

	/** @var int */
	public $health;

	public function decodePayload(){
		$this->health = $this->getVarInt();
	}

	public function encodePayload(){
		$this->putVarInt($this->health);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleSetHealth($this);
	}
}
