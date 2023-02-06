<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\NetworkSession;

class CameraPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::CAMERA_PACKET;

	/** @var int */
	public $cameraUniqueId;
	/** @var int */
	public $playerUniqueId;

	public function decodePayload(){
		$this->cameraUniqueId = $this->getEntityUniqueId();
		$this->playerUniqueId = $this->getEntityUniqueId();
	}

	public function encodePayload(){
		$this->putEntityUniqueId($this->cameraUniqueId);
		$this->putEntityUniqueId($this->playerUniqueId);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleCamera($this);
	}
}