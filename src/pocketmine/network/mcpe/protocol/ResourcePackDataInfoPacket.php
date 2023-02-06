<?php

declare(strict_types=1);


namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\NetworkSession;

class ResourcePackDataInfoPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::RESOURCE_PACK_DATA_INFO_PACKET;

	public $packId;
	public $maxChunkSize;
	public $chunkCount;
	public $compressedPackSize;
	public $sha256;

	public function decodePayload(){
		$this->packId = $this->getString();
		$this->maxChunkSize = $this->getLInt();
		$this->chunkCount = $this->getLInt();
		$this->compressedPackSize = $this->getLLong();
		$this->sha256 = $this->getString();
	}

	public function encodePayload(){
		$this->putString($this->packId);
		$this->putLInt($this->maxChunkSize);
		$this->putLInt($this->chunkCount);
		$this->putLLong($this->compressedPackSize);
		$this->putString($this->sha256);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleResourcePackDataInfo($this);
	}

}