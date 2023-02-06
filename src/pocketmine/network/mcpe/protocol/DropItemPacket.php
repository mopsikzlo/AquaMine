<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>


use pocketmine\item\Item;
use pocketmine\network\NetworkSession;

class DropItemPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::DROP_ITEM_PACKET;

	public $type;
	/** @var Item */
	public $item;

	public function decodePayload(){
		$this->type = $this->getByte();
		$this->item = $this->getSlot();
	}

	public function encodePayload(){
		$this->putByte($this->type);
		$this->putSlot($this->item);
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleDropItem($this);
	}

}
