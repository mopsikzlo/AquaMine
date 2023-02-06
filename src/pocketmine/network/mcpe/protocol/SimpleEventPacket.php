<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\NetworkSession;

class SimpleEventPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::SIMPLE_EVENT_PACKET;

	public $unknownShort1;

	public function decodePayload(){
		$this->unknownShort1 = $this->getLShort();
	}

	public function encodePayload(){
		$this->putLShort($this->unknownShort1);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleSimpleEvent($this);
	}
}