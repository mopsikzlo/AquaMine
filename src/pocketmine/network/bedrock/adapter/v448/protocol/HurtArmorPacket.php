<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\adapter\v448\protocol;

#include <rules/DataPacket.h>

class HurtArmorPacket extends \pocketmine\network\bedrock\protocol\HurtArmorPacket {
	public function decodePayload(){
		$this->cause = $this->getVarInt();
		$this->damage = $this->getVarInt();
	}

	public function encodePayload(){
		$this->putVarInt($this->cause);
		$this->putVarInt($this->damage);
	}
}
