<?php

declare(strict_types=1);


namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\NetworkSession;

use function strlen;

class ResourcePackChunkDataPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::RESOURCE_PACK_CHUNK_DATA_PACKET;

	public $packId;
	public $chunkIndex;
	public $progress;
	public $data;

	public function decodePayload(){
		$this->packId = $this->getString();
		$this->chunkIndex = $this->getLInt();
		$this->progress = $this->getLLong();
		$this->data = $this->get($this->getLInt());
	}

	public function encodePayload(){
		$this->putString($this->packId);
		$this->putLInt($this->chunkIndex);
		$this->putLLong($this->progress);
		$this->putLInt(strlen($this->data));
		$this->put($this->data);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleResourcePackChunkData($this);
	}
}