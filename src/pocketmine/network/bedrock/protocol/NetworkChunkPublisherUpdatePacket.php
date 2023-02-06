<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\NetworkSession;

class NetworkChunkPublisherUpdatePacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::NETWORK_CHUNK_PUBLISHER_UPDATE_PACKET;

	/** @var int */
	public $x;
	/** @var int */
	public $y;
	/** @var int */
	public $z;
	/** @var int */
	public $radius;

	public function decodePayload(){
		$this->getSignedBlockPosition($this->x, $this->y, $this->z);
		$this->radius = $this->getUnsignedVarInt();
	}

	public function encodePayload(){
		$this->putSignedBlockPosition($this->x, $this->y, $this->z);
		$this->putUnsignedVarInt($this->radius);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleNetworkChunkPublisherUpdate($this);
	}
}
