<?php

declare(strict_types=1);


namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\NetworkSession;

class ResourcePackChunkDataPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::RESOURCE_PACK_CHUNK_DATA_PACKET;

	/** @var string */
	public $packId;
	/** @var int */
	public $chunkIndex;
	/** @var int */
	public $offset;
	/** @var string */
	public $data;

	public function decodePayload(){
		$this->packId = $this->getString();
		$this->chunkIndex = $this->getLInt();
		$this->offset = $this->getLLong();
		$this->data = $this->getString();
	}

	public function encodePayload(){
		$this->putString($this->packId);
		$this->putLInt($this->chunkIndex);
		$this->putLLong($this->offset);
		$this->putString($this->data);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleResourcePackChunkData($this);
	}
}
