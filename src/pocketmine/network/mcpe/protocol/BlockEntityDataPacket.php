<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\NetworkSession;

class BlockEntityDataPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::BLOCK_ENTITY_DATA_PACKET;

	public $x;
	public $y;
	public $z;
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
		return $session->handleBlockEntityData($this);
	}

}
