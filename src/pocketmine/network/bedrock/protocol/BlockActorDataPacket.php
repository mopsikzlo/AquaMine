<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\NetworkSession;

class BlockActorDataPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::BLOCK_ACTOR_DATA_PACKET;

	/** @var int */
	public $x;
	/** @var int */
	public $y;
	/** @var int */
	public $z;
	/** @var string */
	public $namedtag;

	public function decodePayload(){
		$this->getBlockPosition($this->x, $this->y, $this->z);
		$this->namedtag = $this->getRemaining();
	}

	public function encodePayload(){
		$this->putBlockPosition($this->x, $this->y, $this->z);
		$this->put($this->namedtag);
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleBlockActorData($this);
	}
}
