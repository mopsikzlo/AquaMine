<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>


use pocketmine\math\Vector3;
use pocketmine\network\NetworkSession;

class SpawnExperienceOrbPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::SPAWN_EXPERIENCE_ORB_PACKET;

	/** @var Vector3 */
	public $position;
	/** @var int */
	public $amount;

	public function decodePayload(){
		$this->position = $this->getVector3();
		$this->amount = $this->getVarInt();
	}

	public function encodePayload(){
		$this->putVector3($this->position);
		$this->putVarInt($this->amount);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleSpawnExperienceOrb($this);
	}
}
