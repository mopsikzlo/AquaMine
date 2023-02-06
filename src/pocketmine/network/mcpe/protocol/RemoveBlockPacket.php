<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\NetworkSession;

class RemoveBlockPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::REMOVE_BLOCK_PACKET;

	public $x;
	public $y;
	public $z;

	public function decodePayload(){
		$this->getBlockPosition($this->x, $this->y, $this->z);
	}

	public function encodePayload(){
		$this->putBlockPosition($this->x, $this->y, $this->z);
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleRemoveBlock($this);
	}

}
