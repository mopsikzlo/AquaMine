<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\NetworkSession;

class SetCommandsEnabledPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::SET_COMMANDS_ENABLED_PACKET;

	public $enabled;

	public function decodePayload(){
		$this->enabled = $this->getBool();
	}

	public function encodePayload(){
		$this->putBool($this->enabled);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleSetCommandsEnabled($this);
	}

}