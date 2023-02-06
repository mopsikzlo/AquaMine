<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\NetworkSession;

class RemoveEntityPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::REMOVE_ENTITY_PACKET;

	public $entityUniqueId;

	public function decodePayload(){
		$this->entityUniqueId = $this->getEntityUniqueId();
	}

	public function encodePayload(){
		$this->putEntityUniqueId($this->entityUniqueId);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleRemoveEntity($this);
	}

}
