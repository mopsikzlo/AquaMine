<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\NetworkSession;

class ChangeDimensionPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::CHANGE_DIMENSION_PACKET;

	/** @var int */
	public $dimension;
	/** @var float */
	public $x;
	/** @var float */
	public $y;
	/** @var float */
	public $z;
	/** @var bool */
	public $respawn = false;

	public function decodePayload(){
		$this->dimension = $this->getVarInt();
		$this->getVector3f($this->x, $this->y, $this->z);
		$this->respawn = $this->getBool();
	}

	public function encodePayload(){
		$this->putVarInt($this->dimension);
		$this->putVector3f($this->x, $this->y, $this->z);
		$this->putBool($this->respawn);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleChangeDimension($this);
	}

}