<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\adapter\v503\protocol;

#include <rules/DataPacket.h>


class PlayerActionPacket extends \pocketmine\network\bedrock\protocol\PlayerActionPacket{

	public function decodePayload(){
		$this->actorRuntimeId = $this->getActorRuntimeId();
		$this->action = $this->getVarInt();
		$this->getBlockPosition($this->x, $this->y, $this->z);
		$this->face = $this->getVarInt();

		[$this->resultX, $this->resultY, $this->resultZ] = [$this->x, $this->y, $this->z];
	}

	public function encodePayload(){
		$this->putActorRuntimeId($this->actorRuntimeId);
		$this->putVarInt($this->action);
		$this->putBlockPosition($this->x, $this->y, $this->z);
		$this->putVarInt($this->face);
	}
}
