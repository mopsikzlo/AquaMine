<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\NetworkSession;

class ScriptCustomEventPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::SCRIPT_CUSTOM_EVENT_PACKET;

	/** @var string */
	public $eventName;
	/** @var string json data */
	public $eventData;

	public function decodePayload(){
		$this->eventName = $this->getString();
		$this->eventData = $this->getString();
	}

	public function encodePayload(){
		$this->putString($this->eventName);
		$this->putString($this->eventData);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleScriptCustomEvent($this);
	}
}
