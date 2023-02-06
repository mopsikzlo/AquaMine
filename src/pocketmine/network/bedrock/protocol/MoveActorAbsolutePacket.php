<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>


use pocketmine\math\Vector3;
use pocketmine\network\NetworkSession;

class MoveActorAbsolutePacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::MOVE_ACTOR_ABSOLUTE_PACKET;

	public const FLAG_GROUND = 0x01;
	public const FLAG_TELEPORT = 0x02;

	/** @var int */
	public $actorRuntimeId;
	/** @var int */
	public $flags = 0;
	/** @var Vector3 */
	public $position;
	/** @var float */
	public $xRot;
	/** @var float */
	public $yRot;
	/** @var float */
	public $zRot;

	public function decodePayload(){
		$this->actorRuntimeId = $this->getActorRuntimeId();
		$this->flags = $this->getByte();
		$this->position = $this->getVector3();
		$this->xRot = $this->getByteRotation();
		$this->yRot = $this->getByteRotation();
		$this->zRot = $this->getByteRotation();
	}

	public function encodePayload(){
		$this->putActorRuntimeId($this->actorRuntimeId);
		$this->putByte($this->flags);
		$this->putVector3($this->position);
		$this->putByteRotation($this->xRot);
		$this->putByteRotation($this->yRot);
		$this->putByteRotation($this->zRot);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleMoveActorAbsolute($this);
	}
}
