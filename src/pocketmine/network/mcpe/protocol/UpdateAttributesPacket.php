<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>


use pocketmine\entity\Attribute;
use pocketmine\network\NetworkSession;

class UpdateAttributesPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::UPDATE_ATTRIBUTES_PACKET;

	public $entityRuntimeId;
	/** @var Attribute[] */
	public $entries = [];

	public function decodePayload(){
		$this->entityRuntimeId = $this->getEntityRuntimeId();
		$this->entries = $this->getAttributeList();
	}

	public function encodePayload(){
		$this->putEntityRuntimeId($this->entityRuntimeId);
		$this->putAttributeList(...$this->entries);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleUpdateAttributes($this);
	}

}
