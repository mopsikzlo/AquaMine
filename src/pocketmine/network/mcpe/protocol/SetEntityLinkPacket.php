<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\NetworkSession;

class SetEntityLinkPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::SET_ENTITY_LINK_PACKET;

	public const TYPE_REMOVE = 0;
	public const TYPE_RIDER = 1;
	public const TYPE_PASSENGER = 2;

	/** @var int */
	public $fromEntityUniqueId;
	/** @var int */
	public $toEntityUniqueId;
	/** @var int */
	public $type;

	public function decodePayload(){
		$this->fromEntityUniqueId = $this->getEntityUniqueId();
		$this->toEntityUniqueId = $this->getEntityUniqueId();
		$this->type = $this->getByte();
	}

	public function encodePayload(){
		$this->putEntityUniqueId($this->fromEntityUniqueId);
		$this->putEntityUniqueId($this->toEntityUniqueId);
		$this->putByte($this->type);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleSetEntityLink($this);
	}

}
