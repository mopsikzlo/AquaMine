<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\NetworkSession;

class LabTablePacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::LAB_TABLE_PACKET;

	/** @var int */
	public $uselessByte; //0 for client -> server, 1 for server -> client. Seems useless.

	/** @var int */
	public $x;
	/** @var int */
	public $y;
	/** @var int */
	public $z;

	/** @var int */
	public $reactionType;

	public function decodePayload(){
		$this->uselessByte = $this->getByte();
		$this->getSignedBlockPosition($this->x, $this->y, $this->z);
		$this->reactionType = $this->getByte();
	}

	public function encodePayload(){
		$this->putByte($this->uselessByte);
		$this->putSignedBlockPosition($this->x, $this->y, $this->z);
		$this->putByte($this->reactionType);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleLabTable($this);
	}
}
