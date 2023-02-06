<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\NetworkSession;

class RemoveActorPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::REMOVE_ACTOR_PACKET;

	/** @var int */
	public $actorUniqueId;

	public function decodePayload(){
		$this->actorUniqueId = $this->getActorUniqueId();
	}

	public function encodePayload(){
		$this->putActorUniqueId($this->actorUniqueId);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleRemoveActor($this);
	}
}
