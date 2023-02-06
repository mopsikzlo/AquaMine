<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\NetworkSession;

class GuiDataPickItemPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::GUI_DATA_PICK_ITEM_PACKET;

	/** @var string */
	public $itemDescription;
	/** @var string */
	public $itemEffects;
	/** @var int */
	public $hotbarSlot;

	public function decodePayload(){
		$this->itemDescription = $this->getString();
		$this->itemEffects = $this->getString();
		$this->hotbarSlot = $this->getLInt();
	}

	public function encodePayload(){
		$this->putString($this->itemDescription);
		$this->putString($this->itemEffects);
		$this->putLInt($this->hotbarSlot);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleGuiDataPickItem($this);
	}
}
