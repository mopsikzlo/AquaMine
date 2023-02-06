<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\NetworkSession;

class MapCreateLockedCopyPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::MAP_CREATE_LOCKED_COPY_PACKET;

	/** @var int */
	public $originalMapId;
	/** @var int */
	public $newMapId;

	public function decodePayload(){
		$this->originalMapId = $this->getVarLong();
		$this->newMapId = $this->getVarLong();
	}

	public function encodePayload(){
		$this->putVarLong($this->originalMapId);
		$this->putVarLong($this->newMapId);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleMapCreateLockedCopy($this);
	}
}
