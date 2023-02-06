<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\NetworkSession;

class SetLocalPlayerAsInitializedPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::SET_LOCAL_PLAYER_AS_INITIALIZED_PACKET;

	/** @var int */
	public $actorRuntimeId;

	public function decodePayload(){
		$this->actorRuntimeId = $this->getActorRuntimeId();
	}

	public function encodePayload(){
		$this->putActorRuntimeId($this->actorRuntimeId);
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleSetLocalPlayerAsInitialized($this);
	}
}
