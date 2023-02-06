<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\NetworkSession;

class ContainerOpenPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::CONTAINER_OPEN_PACKET;

	/** @var int */
	public $windowId;
	/** @var int */
	public $type;
	/** @var int */
	public $x;
	/** @var int */
	public $y;
	/** @var int */
	public $z;
	/** @var int */
	public $actorUniqueId = -1;

	public function decodePayload(){
		$this->windowId = $this->getByte();
		$this->type = $this->getByte();
		$this->getBlockPosition($this->x, $this->y, $this->z);
		$this->actorUniqueId = $this->getActorUniqueId();
	}

	public function encodePayload(){
		$this->putByte($this->windowId);
		$this->putByte($this->type);
		$this->putBlockPosition($this->x, $this->y, $this->z);
		$this->putActorUniqueId($this->actorUniqueId);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleContainerOpen($this);
	}
}
