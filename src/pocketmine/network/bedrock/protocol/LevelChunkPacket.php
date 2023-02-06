<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\NetworkSession;

class LevelChunkPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::LEVEL_CHUNK_PACKET;

	/**
	 * Client will request all subchunks as needed up to the top of the world
	 */
	private const CLIENT_REQUEST_FULL_COLUMN_FAKE_COUNT = 0xffffffff;
	/**
	 * Client will request subchunks as needed up to the height written in the packet, and assume that anything above
	 * that height is air (wtf mojang ...)
	 */
	private const CLIENT_REQUEST_TRUNCATED_COLUMN_FAKE_COUNT = 0xffffffff -1;

	/** @var int */
	public $chunkX;
	/** @var int */
	public $chunkZ;
	/** @var int */
	public $subChunkCount;
	/** @var bool */
	public $clientSubChunkRequestsEnabled = false;
	/** @var bool */
	public $cacheEnabled = false;
	/** @var int[] */
	public $blobIds = [];
	/** @var string */
	public $data;

	public function decodePayload(){
		$this->chunkX = $this->getVarInt();
		$this->chunkZ = $this->getVarInt();

		$subChunkCountButNotReally = $this->getUnsignedVarInt();
		if($subChunkCountButNotReally === self::CLIENT_REQUEST_FULL_COLUMN_FAKE_COUNT){
			$this->clientSubChunkRequestsEnabled = true;
			$this->subChunkCount = PHP_INT_MAX;
		}elseif($subChunkCountButNotReally === self::CLIENT_REQUEST_TRUNCATED_COLUMN_FAKE_COUNT){
			$this->clientSubChunkRequestsEnabled = true;
			$this->subChunkCount = $this->getLShort();
		}else{
			$this->clientSubChunkRequestsEnabled = false;
			$this->subChunkCount = $subChunkCountButNotReally;
		}

		$this->cacheEnabled = $this->getBool();
		if($this->cacheEnabled){
			for($i = 0, $count = $this->getUnsignedVarInt(); $i < $count; ++$i){
				$this->blobIds[] = $this->getLLong();
			}
		}

		$this->data = $this->getString();
	}

	public function encodePayload(){
		$this->putVarInt($this->chunkX);
		$this->putVarInt($this->chunkZ);

		if($this->clientSubChunkRequestsEnabled){
			if($this->subChunkCount === PHP_INT_MAX){
				$this->putUnsignedVarInt(self::CLIENT_REQUEST_FULL_COLUMN_FAKE_COUNT);
			}else{
				$this->putUnsignedVarInt(self::CLIENT_REQUEST_TRUNCATED_COLUMN_FAKE_COUNT);
				$this->putLShort($this->subChunkCount);
			}
		}else{
			$this->putUnsignedVarInt($this->subChunkCount);
		}

		$this->putBool($this->cacheEnabled);
		if($this->cacheEnabled){
			$this->putUnsignedVarInt(count($this->blobIds));
			foreach($this->blobIds as $blobId){
				$this->putLLong($blobId);
			}
		}

		$this->putString($this->data);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleLevelChunk($this);
	}
}
