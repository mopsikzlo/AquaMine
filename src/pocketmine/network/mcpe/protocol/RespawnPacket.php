<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\NetworkSession;

class RespawnPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::RESPAWN_PACKET;

	public $x;
	public $y;
	public $z;

	public function decodePayload(){
		$this->getVector3f($this->x, $this->y, $this->z);
	}

	public function encodePayload(){
		$this->putVector3f($this->x, $this->y, $this->z);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleRespawn($this);
	}

}