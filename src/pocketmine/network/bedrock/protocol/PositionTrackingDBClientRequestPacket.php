<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\NetworkSession;

class PositionTrackingDBClientRequestPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::POSITION_TRACKING_DB_CLIENT_REQUEST_PACKET;

	public const ACTION_QUERY = 0;

	/** @var int */
	public $action;
	/** @var int */
	public $trackingId;

	public function decodePayload(){
		$this->action = $this->getByte();
		$this->trackingId = $this->getVarInt();
	}

	public function encodePayload(){
		$this->putByte($this->action);
		$this->putVarInt($this->trackingId);
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handlePositionTrackingDBClientRequest($this);
	}
}