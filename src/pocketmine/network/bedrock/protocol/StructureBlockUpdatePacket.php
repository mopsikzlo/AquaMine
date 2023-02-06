<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\NetworkSession;

class StructureBlockUpdatePacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::STRUCTURE_BLOCK_UPDATE_PACKET;

	public function decodePayload(){
		//TODO
	}

	public function encodePayload(){
		//TODO
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleStructureBlockUpdate($this);
	}
}
