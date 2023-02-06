<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\NetworkSession;

class ShowProfilePacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::SHOW_PROFILE_PACKET;

	/** @var string */
	public $xuid;

	public function decodePayload(){
		$this->xuid = $this->getString();
	}

	public function encodePayload(){
		$this->putString($this->xuid);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleShowProfile($this);
	}
}
