<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\NetworkSession;

class SetEntityDataPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::SET_ENTITY_DATA_PACKET;

	public $entityRuntimeId;
	public $metadata;

	public function decodePayload(){
		$this->entityRuntimeId = $this->getEntityRuntimeId();
		$this->metadata = $this->getEntityMetadata();
	}

	public function encodePayload(){
		$this->putEntityRuntimeId($this->entityRuntimeId);
		$this->putEntityMetadata($this->metadata);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleSetEntityData($this);
	}
}
