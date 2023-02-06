<?php

declare(strict_types=1);


namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\NetworkSession;

class BlockPickRequestPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::BLOCK_PICK_REQUEST_PACKET;

	public $tileX;
	public $tileY;
	public $tileZ;
	public $hotbarSlot;

	public function decodePayload(){
		$this->getSignedBlockPosition($this->tileX, $this->tileY, $this->tileZ);
		$this->hotbarSlot = $this->getByte();
	}

	public function encodePayload(){
		$this->putSignedBlockPosition($this->tileX, $this->tileY, $this->tileZ);
		$this->putByte($this->hotbarSlot);
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleBlockPickRequest($this);
	}
}