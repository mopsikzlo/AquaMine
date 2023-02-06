<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\adapter\v486\protocol;

#include <rules/DataPacket.h>

class SpawnParticleEffectPacket extends \pocketmine\network\bedrock\protocol\SpawnParticleEffectPacket{

	public function decodePayload(){
		$this->dimensionId = $this->getByte();
		$this->actorUniqueId = $this->getActorUniqueId();
		$this->position = $this->getVector3();
		$this->particleName = $this->getString();
	}

	public function encodePayload(){
		$this->putByte($this->dimensionId);
		$this->putActorUniqueId($this->actorUniqueId);
		$this->putVector3($this->position);
		$this->putString($this->particleName);
	}
}