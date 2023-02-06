<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\NetworkSession;

class CameraPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::CAMERA_PACKET;

	/** @var int */
	public $cameraUniqueId;
	/** @var int */
	public $playerUniqueId;

	public function decodePayload(){
		$this->cameraUniqueId = $this->getActorUniqueId();
		$this->playerUniqueId = $this->getActorUniqueId();
	}

	public function encodePayload(){
		$this->putActorUniqueId($this->cameraUniqueId);
		$this->putActorUniqueId($this->playerUniqueId);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleCamera($this);
	}
}
