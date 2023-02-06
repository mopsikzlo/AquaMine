<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\NetworkSession;

class ContainerClosePacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::CONTAINER_CLOSE_PACKET;

	public $windowId;

	public function decodePayload(){
		$this->windowId = $this->getByte();
	}

	public function encodePayload(){
		$this->putByte($this->windowId);
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleContainerClose($this);
	}
}