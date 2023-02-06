<?php

declare(strict_types=1);


namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\NetworkSession;

class ResourcePackDataInfoPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::RESOURCE_PACK_DATA_INFO_PACKET;

	public const TYPE_INVALID = 0;
	public const TYPE_ADDON = 1;
	public const TYPE_CACHED = 2;
	public const TYPE_COPY_PROTECTED = 3;
	public const TYPE_BEHAVIOR = 4;
	public const TYPE_PERSONA_PIECE = 5;
	public const TYPE_RESOURCE = 6;
	public const TYPE_SKINS = 7;
	public const TYPE_WORLD_TEMPLATE = 8;
	public const TYPE_COUNT = 9;

	/** @var string */
	public $packId;
	/** @var int */
	public $maxChunkSize;
	/** @var int */
	public $chunkCount;
	/** @var int */
	public $compressedPackSize;
	/** @var string */
	public $sha256;
	/** @var bool */
	public $isPremium = false;
	/** @var int */
	public $type = self::TYPE_RESOURCE;

	public function decodePayload(){
		$this->packId = $this->getString();
		$this->maxChunkSize = $this->getLInt();
		$this->chunkCount = $this->getLInt();
		$this->compressedPackSize = $this->getLLong();
		$this->sha256 = $this->getString();
		$this->isPremium = $this->getBool();
		$this->type = $this->getByte();
	}

	public function encodePayload(){
		$this->putString($this->packId);
		$this->putLInt($this->maxChunkSize);
		$this->putLInt($this->chunkCount);
		$this->putLLong($this->compressedPackSize);
		$this->putString($this->sha256);
		$this->putBool($this->isPremium);
		$this->putByte($this->type);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleResourcePackDataInfo($this);
	}
}
