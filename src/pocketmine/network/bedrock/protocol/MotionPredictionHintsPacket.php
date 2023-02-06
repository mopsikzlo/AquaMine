<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>


use pocketmine\math\Vector3;
use pocketmine\network\NetworkSession;

class MotionPredictionHintsPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::MOTION_PREDICTION_HINTS_PACKET;

	/** @var int */
	public $actorRuntimeId;
	/** @var Vector3 */
	public $motion;
	/** @var bool */
	public $onGround;

	public function decodePayload(){
		$this->actorRuntimeId = $this->getActorRuntimeId();
		$this->motion = $this->getVector3();
		$this->onGround = $this->getBool();
	}

	public function encodePayload(){
		$this->putActorRuntimeId($this->actorRuntimeId);
		$this->putVector3($this->motion);
		$this->putBool($this->onGround);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleCameraShake($this);
	}
}
