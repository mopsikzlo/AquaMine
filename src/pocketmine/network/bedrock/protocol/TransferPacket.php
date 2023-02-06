<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\NetworkSession;

class TransferPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::TRANSFER_PACKET;

	/** @var string */
	public $address;
	/** @var int */
	public $port = 19132;

	public function decodePayload(){
		$this->address = $this->getString();
		$this->port = $this->getLShort();
	}

	public function encodePayload(){
		$this->putString($this->address);
		$this->putLShort($this->port);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleTransfer($this);
	}
}
