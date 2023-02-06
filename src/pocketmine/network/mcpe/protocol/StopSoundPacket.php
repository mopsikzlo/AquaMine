<?php

declare(strict_types=1);


namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\NetworkSession;

class StopSoundPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::STOP_SOUND_PACKET;

	public $soundName;
	public $stopAll;

	public function decodePayload(){
		$this->soundName = $this->getString();
		$this->stopAll = $this->getBool();
	}

	public function encodePayload(){
		$this->putString($this->soundName);
		$this->putBool($this->stopAll);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleStopSound($this);
	}
}