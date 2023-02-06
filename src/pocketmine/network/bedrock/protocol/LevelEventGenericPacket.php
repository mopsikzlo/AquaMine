<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\NetworkSession;

class LevelEventGenericPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::LEVEL_EVENT_GENERIC_PACKET;

	/** @var int */
	public $eventId;
	/** @var string */
	public $namedtag;

	public function decodePayload(){
		$this->eventId = $this->getVarInt();
		$this->namedtag = $this->getRemaining();
	}

	public function encodePayload(){
		$this->putVarInt($this->eventId);
		$this->put($this->namedtag);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleLevelEventGeneric($this);
	}
}
