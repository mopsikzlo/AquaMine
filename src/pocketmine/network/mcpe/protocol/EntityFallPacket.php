<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\NetworkSession;

class EntityFallPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::ENTITY_FALL_PACKET;

	public $entityRuntimeId;
	public $fallDistance;
	public $bool1;

	public function decodePayload(){
		$this->entityRuntimeId = $this->getEntityRuntimeId();
		$this->fallDistance = $this->getLFloat();
		$this->bool1 = $this->getBool();
	}

	public function encodePayload(){
		$this->putEntityRuntimeId($this->entityRuntimeId);
		$this->putLFloat($this->fallDistance);
		$this->putBool($this->bool1);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleEntityFall($this);
	}
}
