<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\NetworkSession;

class CameraShakePacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::CAMERA_SHAKE_PACKET;

	public const TYPE_POSITIONAL = 0;
	public const TYPE_ROTATIONAL = 1;

	public const ACTION_ADD = 0;
	public const ACTION_STOP = 1;

	/** @var float */
	public $intensity;
	/** @var float */
	public $duration;
	/** @var int */
	public $shakeType;
	/** @var int */
	public $shakeAction;

	public function decodePayload(){
		$this->intensity = $this->getLFloat();
		$this->duration = $this->getLFloat();
		$this->shakeType = $this->getByte();
		$this->shakeAction = $this->getByte();
	}

	public function encodePayload(){
		$this->putLFloat($this->intensity);
		$this->putLFloat($this->duration);
		$this->putByte($this->shakeType);
		$this->putByte($this->shakeAction);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleCameraShake($this);
	}
}
