<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\NetworkSession;

class SetActorDataPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::SET_ACTOR_DATA_PACKET;

	/** @var int */
	public $actorRuntimeId;
	/** @var array */
	public $metadata;

	/** @var int */
	public $tick = 0;

	public function decodePayload(){
		$this->actorRuntimeId = $this->getActorRuntimeId();
		$this->metadata = $this->getActorMetadata();
		$this->tick = $this->getUnsignedVarLong();
	}

	public function encodePayload(){
		$this->putActorRuntimeId($this->actorRuntimeId);
		$this->putActorMetadata($this->metadata);
		$this->putUnsignedVarLong($this->tick);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleSetActorData($this);
	}
}
