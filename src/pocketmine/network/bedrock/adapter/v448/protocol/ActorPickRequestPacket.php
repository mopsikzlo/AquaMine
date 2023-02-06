<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\adapter\v448\protocol;

#include <rules/DataPacket.h>

class ActorPickRequestPacket extends \pocketmine\network\bedrock\protocol\ActorPickRequestPacket {
	public function decodePayload(){
		$this->actorUniqueId = $this->getLLong();
		$this->hotbarSlot = $this->getByte();
	}

	public function encodePayload(){
		$this->putLLong($this->actorUniqueId);
		$this->putByte($this->hotbarSlot);
	}
}
