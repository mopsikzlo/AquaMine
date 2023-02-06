<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\NetworkSession;

class ClientCacheBlobStatusPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::CLIENT_CACHE_BLOB_STATUS_PACKET;

	/** @var int[] xxHash64 subchunk data hashes */
	public $hitHashes = [];
	/** @var int[] xxHash64 subchunk data hashes */
	public $missHashes = [];

	public function decodePayload(){
		$hitCount = $this->getUnsignedVarInt();
		$missCount = $this->getUnsignedVarInt();
		for($i = 0; $i < $hitCount; ++$i){
			$this->hitHashes[] = $this->getLLong();
		}
		for($i = 0; $i < $missCount; ++$i){
			$this->missHashes[] = $this->getLLong();
		}
	}

	public function encodePayload(){
		$this->putUnsignedVarInt(count($this->hitHashes));
		$this->putUnsignedVarInt(count($this->missHashes));
		foreach($this->hitHashes as $hash){
			$this->putLLong($hash);
		}
		foreach($this->missHashes as $hash){
			$this->putLLong($hash);
		}
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleClientCacheBlobStatus($this);
	}
}
