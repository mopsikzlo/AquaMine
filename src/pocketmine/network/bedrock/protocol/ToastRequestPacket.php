<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\NetworkSession;

class ToastRequestPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::TOAST_REQUEST_PACKET;

	/** @var string */
	public $title;
	/** @var string */
	public $body;

	public function decodePayload(){
		$this->title = $this->getString();
		$this->body = $this->getString();
	}

	public function encodePayload(){
		$this->putString($this->title);
		$this->putString($this->body);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleToastRequest($this);
	}
}
