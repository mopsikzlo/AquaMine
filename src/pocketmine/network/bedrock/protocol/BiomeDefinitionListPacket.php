<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\NetworkSession;

class BiomeDefinitionListPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::BIOME_DEFINITION_LIST_PACKET;

	/** @var string */
	public $namedtag;

	public function decodePayload(){
		$this->namedtag = $this->getRemaining();
	}

	public function encodePayload(){
		$this->put($this->namedtag);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleBiomeDefinitionList($this);
	}
}
