<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\NetworkSession;

class TakeItemEntityPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::TAKE_ITEM_ENTITY_PACKET;

	public $target;
	public $eid;

	public function decodePayload(){
		$this->target = $this->getEntityRuntimeId();
		$this->eid = $this->getEntityRuntimeId();
	}

	public function encodePayload(){
		$this->putEntityRuntimeId($this->target);
		$this->putEntityRuntimeId($this->eid);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleTakeItemEntity($this);
	}
}
