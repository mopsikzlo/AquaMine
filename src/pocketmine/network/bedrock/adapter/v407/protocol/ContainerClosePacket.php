<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\adapter\v407\protocol;

#include <rules/DataPacket.h>


class ContainerClosePacket extends \pocketmine\network\bedrock\protocol\ContainerClosePacket{
	use PacketTrait;

	public function decodePayload(){
		$this->windowId = $this->getByte();
	}

	public function encodePayload(){
		$this->putByte($this->windowId);
	}
}