<?php

declare(strict_types=1);


namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\NetworkSession;

class RiderJumpPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::RIDER_JUMP_PACKET;

	/** @var int */
	public $jumpStrength; //percentage

	public function decodePayload(){
		$this->jumpStrength = $this->getVarInt();
	}

	public function encodePayload(){
		$this->putVarInt($this->jumpStrength);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleRiderJump($this);
	}
}
