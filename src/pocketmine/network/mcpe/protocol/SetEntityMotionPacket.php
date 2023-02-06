<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\NetworkSession;

class SetEntityMotionPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::SET_ENTITY_MOTION_PACKET;

	public $entityRuntimeId;
	public $motionX;
	public $motionY;
	public $motionZ;

	public function decodePayload(){
		$this->entityRuntimeId = $this->getEntityRuntimeId();
		$this->getVector3f($this->motionX, $this->motionY, $this->motionZ);
	}

	public function encodePayload(){
		$this->putEntityRuntimeId($this->entityRuntimeId);
		$this->putVector3f($this->motionX, $this->motionY, $this->motionZ);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleSetEntityMotion($this);
	}

}
