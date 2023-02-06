<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\NetworkSession;

class AddBehaviorTreePacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::ADD_BEHAVIOR_TREE_PACKET;

	/** @var string */
	public $behaviorTreeJson;

	public function decodePayload(){
		$this->behaviorTreeJson = $this->getString();
	}

	public function encodePayload(){
		$this->putString($this->behaviorTreeJson);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleAddBehaviorTree($this);
	}
}
