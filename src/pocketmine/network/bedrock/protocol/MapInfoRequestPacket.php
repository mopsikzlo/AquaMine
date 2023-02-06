<?php

declare(strict_types=1);


namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\NetworkSession;

class MapInfoRequestPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::MAP_INFO_REQUEST_PACKET;

	/** @var int */
	public $mapId;

	public function decodePayload(){
		$this->mapId = $this->getActorUniqueId();
	}

	public function encodePayload(){
		$this->putActorUniqueId($this->mapId);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleMapInfoRequest($this);
	}
}
