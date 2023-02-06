<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\NetworkSession;

class PositionTrackingDBServerBroadcastPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::POSITION_TRACKING_DB_SERVER_BROADCAST_PACKET;

	public const ACTION_UPDATE = 0;
	public const ACTION_DESTROY = 1;
	public const ACTION_NOT_FOUND = 2;

	/** @var int */
	public $action;
	/** @var int */
	public $trackingId;
	/** @var string */
	public $serializedData;

	public function decodePayload(){
		$this->action = $this->getByte();
		$this->trackingId = $this->getVarInt();
		$this->serializedData = $this->getRemaining();
	}

	public function encodePayload(){
		$this->putByte($this->action);
		$this->putVarInt($this->trackingId);
		$this->put($this->serializedData);
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handlePositionTrackingDBServerBroadcast($this);
	}
}