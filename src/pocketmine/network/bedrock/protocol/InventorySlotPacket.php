<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>

use pocketmine\item\Item;
use pocketmine\network\bedrock\protocol\types\inventory\ItemInstance;
use pocketmine\network\NetworkSession;

class InventorySlotPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::INVENTORY_SLOT_PACKET;

	/** @var int */
	public $windowId;
	/** @var int */
	public $inventorySlot;
	/** @var ItemInstance */
	public $item;

	public function decodePayload(){
		$this->windowId = $this->getUnsignedVarInt();
		$this->inventorySlot = $this->getUnsignedVarInt();
		$this->item = $this->getItemInstance();
	}

	public function encodePayload(){
		$this->putUnsignedVarInt($this->windowId);
		$this->putUnsignedVarInt($this->inventorySlot);
		$this->putItemInstance($this->item);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleInventorySlot($this);
	}
}
