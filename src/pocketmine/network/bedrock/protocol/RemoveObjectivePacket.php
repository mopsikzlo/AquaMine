<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\NetworkSession;

class RemoveObjectivePacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::REMOVE_OBJECTIVE_PACKET;

	/** @var string */
	public $objectiveName;

	public function decodePayload(){
		$this->objectiveName = $this->getString();
	}

	public function encodePayload(){
		$this->putString($this->objectiveName);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleRemoveObjective($this);
	}
}
