<?php

declare(strict_types=1);


namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\NetworkSession;

class ResourcePackChunkRequestPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::RESOURCE_PACK_CHUNK_REQUEST_PACKET;

	public $packId;
	public $chunkIndex;

	public function decodePayload(){
		$this->packId = $this->getString();
		$this->chunkIndex = $this->getLInt();
	}

	public function encodePayload(){
		$this->putString($this->packId);
		$this->putLInt($this->chunkIndex);
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleResourcePackChunkRequest($this);
	}
}