<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\NetworkSession;

class EmotePacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::EMOTE_PACKET;

	public const FLAG_SERVER_SIDE = 0x01;

	/** @var int */
	public $actorRuntimeId;
	/** @var string */
	public $emoteId;
	/** @var int */
	public $flags = 0;

	public function decodePayload(){
		$this->actorRuntimeId = $this->getActorRuntimeId();
		$this->emoteId = $this->getString();
		$this->flags = $this->getByte();
	}

	public function encodePayload(){
		$this->putActorRuntimeId($this->actorRuntimeId);
		$this->putString($this->emoteId);
		$this->putByte($this->flags);
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleEmote($this);
	}
}