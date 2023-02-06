<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\NetworkSession;

class AddHangingEntityPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::ADD_HANGING_ENTITY_PACKET;

	public $entityUniqueId;
	public $entityRuntimeId;
	public $x;
	public $y;
	public $z;
	public $unknown; //TODO (rotation?)

	public function decodePayload(){
		$this->entityUniqueId = $this->getEntityUniqueId();
		$this->entityRuntimeId = $this->getEntityRuntimeId();
		$this->getBlockPosition($this->x, $this->y, $this->z);
		$this->unknown = $this->getVarInt();
	}

	public function encodePayload(){
		$this->putEntityUniqueId($this->entityUniqueId);
		$this->putEntityRuntimeId($this->entityRuntimeId);
		$this->putBlockPosition($this->x, $this->y, $this->z);
		$this->putVarInt($this->unknown);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleAddHangingEntity($this);
	}

}