<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>


use pocketmine\math\Vector3;
use pocketmine\network\NetworkSession;

class RespawnPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::RESPAWN_PACKET;

	public const STATE_SEARCHING_FOR_SPAWN = 0;
	public const STATE_READY_TO_SPAWN = 1;
	public const STATE_CLIENT_READY_TO_SPAWN = 2;

	/** @var Vector3 */
	public $position;
	/** @var int */
	public $respawnState;
	/** @var int */
	public $actorRuntimeId;

	public function decodePayload(){
		$this->position = $this->getVector3();
		$this->respawnState = $this->getByte();
		$this->actorRuntimeId = $this->getActorRuntimeId();
	}

	public function encodePayload(){
		$this->putVector3($this->position);
		$this->putByte($this->respawnState);
		$this->putActorRuntimeId($this->actorRuntimeId);
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleRespawn($this);
	}
}
