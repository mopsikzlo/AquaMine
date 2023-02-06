<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>


use pocketmine\math\Vector3;
use pocketmine\network\NetworkSession;

class ChangeDimensionPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::CHANGE_DIMENSION_PACKET;

	/** @var int */
	public $dimension;
	/** @var Vector3 */
	public $position;
	/** @var bool */
	public $respawn = false;

	public function decodePayload(){
		$this->dimension = $this->getVarInt();
		$this->position = $this->getVector3();
		$this->respawn = $this->getBool();
	}

	public function encodePayload(){
		$this->putVarInt($this->dimension);
		$this->putVector3($this->position);
		$this->putBool($this->respawn);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleChangeDimension($this);
	}
}
