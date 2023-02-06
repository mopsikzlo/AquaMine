<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\adapter\v448\protocol;

#include <rules/DataPacket.h>

class PhotoTransferPacket extends \pocketmine\network\bedrock\protocol\PhotoTransferPacket {
	public function decodePayload(){
		$this->photoName = $this->getString();
		$this->photoData = $this->getString();
		$this->bookId = $this->getString();
	}

	public function encodePayload(){
		$this->putString($this->photoName);
		$this->putString($this->photoData);
		$this->putString($this->bookId);
	}

}

