<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\NetworkSession;

class SetTimePacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::SET_TIME_PACKET;

	public $time;

	public function decodePayload(){
		$this->time = $this->getVarInt();
	}

	public function encodePayload(){
		$this->putVarInt($this->time);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleSetTime($this);
	}

}
