<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\NetworkSession;

class ContainerOpenPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::CONTAINER_OPEN_PACKET;

	public $windowId;
	public $type;
	public $x;
	public $y;
	public $z;
	public $entityUniqueId = -1;

	public function decodePayload(){
		$this->windowId = $this->getByte();
		$this->type = $this->getByte();
		$this->getBlockPosition($this->x, $this->y, $this->z);
		$this->entityUniqueId = $this->getEntityUniqueId();
	}

	public function encodePayload(){
		$this->putByte($this->windowId);
		$this->putByte($this->type);
		$this->putBlockPosition($this->x, $this->y, $this->z);
		$this->putEntityUniqueId($this->entityUniqueId);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleContainerOpen($this);
	}

}