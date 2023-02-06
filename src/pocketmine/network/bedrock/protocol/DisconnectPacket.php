<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\NetworkSession;

class DisconnectPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::DISCONNECT_PACKET;

	/** @var bool */
	public $hideDisconnectionScreen = false;
	/** @var string */
	public $message = "";

	public function canBeSentBeforeLogin() : bool{
		return true;
	}

	public function decodePayload(){
		$this->hideDisconnectionScreen = $this->getBool();
		if(!$this->hideDisconnectionScreen){
			$this->message = $this->getString();
		}
	}

	public function encodePayload(){
		$this->putBool($this->hideDisconnectionScreen);
		if(!$this->hideDisconnectionScreen){
			$this->putString($this->message);
		}
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleDisconnect($this);
	}
}
