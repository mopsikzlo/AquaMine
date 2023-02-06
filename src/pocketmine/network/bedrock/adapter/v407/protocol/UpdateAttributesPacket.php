<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\adapter\v407\protocol;

#include <rules/DataPacket.h>


use function array_values;

class UpdateAttributesPacket extends \pocketmine\network\bedrock\protocol\UpdateAttributesPacket{
	use PacketTrait;

	public function decodePayload(){
		$this->actorRuntimeId = $this->getActorRuntimeId();
		$this->entries = $this->getAttributeList();
	}

	public function encodePayload(){
		$this->putActorRuntimeId($this->actorRuntimeId);
		$this->putAttributeList(...array_values($this->entries));
	}
}