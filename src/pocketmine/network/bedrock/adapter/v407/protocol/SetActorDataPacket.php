<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\adapter\v407\protocol;

#include <rules/DataPacket.h>


class SetActorDataPacket extends \pocketmine\network\bedrock\protocol\SetActorDataPacket{
	use PacketTrait;

	public function decodePayload(){
		$this->actorRuntimeId = $this->getActorRuntimeId();
		$this->metadata = $this->getActorMetadata();
	}

	public function encodePayload(){
		$this->putActorRuntimeId($this->actorRuntimeId);
		$this->putActorMetadata($this->metadata);
	}
}