<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\NetworkSession;

class SetLastHurtByPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::SET_LAST_HURT_BY_PACKET;

	/** @var int */
	public $actorType;

	public function decodePayload(){
		$this->actorType = $this->getVarInt();
	}

	public function encodePayload(){
		$this->putVarInt($this->actorType);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleSetLastHurtBy($this);
	}
}
