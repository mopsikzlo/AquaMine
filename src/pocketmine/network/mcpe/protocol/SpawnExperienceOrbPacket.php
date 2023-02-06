<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\NetworkSession;

class SpawnExperienceOrbPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::SPAWN_EXPERIENCE_ORB_PACKET;

	public $x;
	public $y;
	public $z;
	public $amount;

	public function decodePayload(){
		$this->getVector3f($this->x, $this->y, $this->z);
		$this->amount = $this->getVarInt();
	}

	public function encodePayload(){
		$this->putVector3f($this->x, $this->y, $this->z);
		$this->putVarInt($this->amount);
	}
	
	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleSpawnExperienceOrb($this);
	}
}