<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\NetworkSession;

class EventPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::EVENT_PACKET;

	public const TYPE_ACHIEVEMENT_AWARDED = 0;
	public const TYPE_ACTOR_INTERACT = 1;
	public const TYPE_PORTAL_BUILT = 2;
	public const TYPE_PORTAL_USED = 3;
	public const TYPE_MOB_KILLED = 4;
	public const TYPE_CAULDRON_USED = 5;
	public const TYPE_PLAYER_DEATH = 6;
	public const TYPE_BOSS_KILLED = 7;
	public const TYPE_AGENT_COMMAND = 8;
	public const TYPE_AGENT_CREATED = 9;
	public const TYPE_PATTERN_REMOVED = 10; //???
	public const TYPE_COMMANED_EXECUTED = 11;
	public const TYPE_FISH_BUCKETED = 12;

	/** @var int */
	public $playerRuntimeId;
	/** @var int */
	public $eventData;
	/** @var int */
	public $type;

	public function decodePayload(){
		$this->playerRuntimeId = $this->getActorRuntimeId();
		$this->eventData = $this->getVarInt();
		$this->type = $this->getByte();

		//TODO: nice confusing mess
	}

	public function encodePayload(){
		$this->putActorRuntimeId($this->playerRuntimeId);
		$this->putVarInt($this->eventData);
		$this->putByte($this->type);

		//TODO: also nice confusing mess
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleEvent($this);
	}
}
