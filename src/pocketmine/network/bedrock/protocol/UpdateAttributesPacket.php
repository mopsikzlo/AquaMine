<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>


use pocketmine\entity\Attribute;
use pocketmine\network\NetworkSession;
use function array_values;

class UpdateAttributesPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::UPDATE_ATTRIBUTES_PACKET;

	/** @var int */
	public $actorRuntimeId;
	/** @var Attribute[] */
	public $entries = [];
	/** @var int */
	public $tick = 0;

	public function decodePayload(){
		$this->actorRuntimeId = $this->getActorRuntimeId();
		$this->entries = $this->getAttributeList();
		$this->tick = $this->getUnsignedVarLong();
	}

	public function encodePayload(){
		$this->putActorRuntimeId($this->actorRuntimeId);
		$this->putAttributeList(...array_values($this->entries));
		$this->putUnsignedVarLong($this->tick);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleUpdateAttributes($this);
	}
}
