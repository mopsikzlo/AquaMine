<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\NetworkSession;

class TickSyncPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::TICK_SYNC_PACKET;

	/** @var int */
	public $requestTimeStamp;
	/** @var int */
	public $responseTimeStamp;

	public function decodePayload(){
		$this->requestTimeStamp = $this->getLLong();
		$this->responseTimeStamp = $this->getLLong();
	}

	public function encodePayload(){
		$this->putLLong($this->requestTimeStamp);
		$this->putLLong($this->requestTimeStamp);
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleTickSync($this);
	}
}