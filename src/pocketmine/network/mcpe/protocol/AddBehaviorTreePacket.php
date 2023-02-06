<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\NetworkSession;

class AddBehaviorTreePacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::ADD_BEHAVIOR_TREE_PACKET;

	/** @var string */
	public $unknownString1;

	public function decodePayload(){
		$this->unknownString1 = $this->getString();
	}

	public function encodePayload(){
		$this->putString($this->unknownString1);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleAddBehaviorTree($this);
	}
}