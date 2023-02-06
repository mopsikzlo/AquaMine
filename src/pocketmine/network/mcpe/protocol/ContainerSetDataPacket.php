<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\NetworkSession;

class ContainerSetDataPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::CONTAINER_SET_DATA_PACKET;

	public $windowId;
	public $property;
	public $value;

	public function decodePayload(){
		$this->windowId = $this->getByte();
		$this->property = $this->getVarInt();
		$this->value = $this->getVarInt();
	}

	public function encodePayload(){
		$this->putByte($this->windowId);
		$this->putVarInt($this->property);
		$this->putVarInt($this->value);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleContainerSetData($this);
	}

}