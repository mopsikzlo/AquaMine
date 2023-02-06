<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\adapter\v440\protocol;


#include <rules/DataPacket.h>

use pocketmine\network\NetworkSession;

class NpcRequestPacket extends \pocketmine\network\bedrock\protocol\NpcRequestPacket {
	public function decodePayload(){
		$this->actorRuntimeId = $this->getActorRuntimeId();
		$this->requestType = $this->getByte();
		$this->commandString = $this->getString();
		$this->actionType = $this->getByte();
	}

	public function encodePayload(){
		$this->putActorRuntimeId($this->actorRuntimeId);
		$this->putByte($this->requestType);
		$this->putString($this->commandString);
		$this->putByte($this->actionType);
	}
}
