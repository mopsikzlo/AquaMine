<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\NetworkSession;

class TakeItemActorPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::TAKE_ITEM_ACTOR_PACKET;

	/** @var int */
	public $targetRuntimeId;
	/** @var int */
	public $actorRuntimeId;

	public function decodePayload(){
		$this->targetRuntimeId = $this->getActorRuntimeId();
		$this->actorRuntimeId = $this->getActorRuntimeId();
	}

	public function encodePayload(){
		$this->putActorRuntimeId($this->targetRuntimeId);
		$this->putActorRuntimeId($this->actorRuntimeId);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleTakeItemActor($this);
	}
}
