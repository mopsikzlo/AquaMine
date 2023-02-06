<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\bedrock\protocol\types\inventory\ItemInstance;
use pocketmine\network\NetworkSession;
use function count;

class InventoryContentPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::INVENTORY_CONTENT_PACKET;

	/** @var int */
	public $windowId;
	/** @var ItemInstance[] */
	public $items = [];

	public function decodePayload(){
		$this->windowId = $this->getUnsignedVarInt();
		$count = $this->getUnsignedVarInt();
		for($i = 0; $i < $count; ++$i){
			$this->items[] = $this->getItemInstance();
		}
	}

	public function encodePayload(){
		$this->putUnsignedVarInt($this->windowId);
		$this->putUnsignedVarInt(count($this->items));
		foreach($this->items as $item){
			$this->putItemInstance($item);
		}
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleInventoryContent($this);
	}
}
