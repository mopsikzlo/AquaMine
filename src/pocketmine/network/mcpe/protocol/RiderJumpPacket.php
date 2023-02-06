<?php

declare(strict_types=1);


namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\NetworkSession;

class RiderJumpPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::RIDER_JUMP_PACKET;

	public $unknown;

	public function decodePayload(){
		$this->unknown = $this->getVarInt();
	}

	public function encodePayload(){
		$this->putVarInt($this->unknown);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleRiderJump($this);
	}
}