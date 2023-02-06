<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\NetworkSession;

class ContainerClosePacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::CONTAINER_CLOSE_PACKET;

	/** @var int */
	public $windowId;
	/** @var bool */
	public $server = false;

	public function decodePayload(){
		$this->windowId = $this->getByte();
		$this->server = $this->getBool();
	}

	public function encodePayload(){
		$this->putByte($this->windowId);
		$this->putBool($this->server);
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleContainerClose($this);
	}
}
