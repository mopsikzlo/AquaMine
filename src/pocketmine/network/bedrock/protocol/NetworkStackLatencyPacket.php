<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\NetworkSession;

class NetworkStackLatencyPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::NETWORK_STACK_LATENCY_PACKET;

	/** @var int */
	public $timestamp;
	/** @var bool */
	public $needResponse = false;

	public function decodePayload(){
		$this->timestamp = $this->getLLong();
		$this->needResponse = $this->getBool();
	}

	public function encodePayload(){
		$this->putLLong($this->timestamp);
		$this->putBool($this->needResponse);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleNetworkStackLatency($this);
	}
}
