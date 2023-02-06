<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\NetworkSession;

class ActorPickRequestPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::ACTOR_PICK_REQUEST_PACKET;

	/** @var int */
	public $actorUniqueId;
	/** @var int */
	public $hotbarSlot;

	public function decodePayload(){
		$this->actorUniqueId = $this->getLLong();
		$this->hotbarSlot = $this->getByte();
	}

	public function encodePayload(){
		$this->putLLong($this->actorUniqueId);
		$this->putByte($this->hotbarSlot);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleActorPickRequest($this);
	}
}
