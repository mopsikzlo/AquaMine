<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\NetworkSession;

class BlockEventPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::BLOCK_EVENT_PACKET;

	//TODO: more constants
	public const TYPE_CHEST = 1;

	public const DATA_CHEST_CLOSED = 0;
	public const DATA_CHEST_OPEN = 1;

	/** @var int */
	public $x;
	/** @var int */
	public $y;
	/** @var int */
	public $z;
	/** @var int */
	public $eventType;
	/** @var int */
	public $eventData;

	public function decodePayload(){
		$this->getBlockPosition($this->x, $this->y, $this->z);
		$this->eventType = $this->getVarInt();
		$this->eventData = $this->getVarInt();
	}

	public function encodePayload(){
		$this->putBlockPosition($this->x, $this->y, $this->z);
		$this->putVarInt($this->eventType);
		$this->putVarInt($this->eventData);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleBlockEvent($this);
	}
}
