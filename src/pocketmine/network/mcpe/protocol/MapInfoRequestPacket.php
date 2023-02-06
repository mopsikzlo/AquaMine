<?php

declare(strict_types=1);


namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\NetworkSession;

class MapInfoRequestPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::MAP_INFO_REQUEST_PACKET;

	public $mapId;

	public function decodePayload(){
		$this->mapId = $this->getEntityUniqueId();
	}

	public function encodePayload(){
		$this->putEntityUniqueId($this->mapId);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleMapInfoRequest($this);
	}
}