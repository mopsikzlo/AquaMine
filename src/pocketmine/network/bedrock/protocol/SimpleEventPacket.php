<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\NetworkSession;

class SimpleEventPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::SIMPLE_EVENT_PACKET;

	public const TYPE_ENABLE_COMMANDS = 1;
	public const TYPE_DISABLE_COMMANDS = 2;

	/** @var int */
	public $eventType;

	public function decodePayload(){
		$this->eventType = $this->getLShort();
	}

	public function encodePayload(){
		$this->putLShort($this->eventType);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleSimpleEvent($this);
	}
}
