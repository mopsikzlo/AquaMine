<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\NetworkSession;

class ReplaceItemInSlotPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::REPLACE_ITEM_IN_SLOT_PACKET;

	public $item;

	public function decodePayload(){
		$this->item = $this->getSlot();
	}

	public function encodePayload(){
		$this->putSlot($this->item);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleReplaceItemInSlot($this);
	}
}