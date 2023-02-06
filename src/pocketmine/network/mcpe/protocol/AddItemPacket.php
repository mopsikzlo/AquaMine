<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\NetworkSession;

class AddItemPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::ADD_ITEM_PACKET;

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
		return $session->handleAddItem($this);
	}

}