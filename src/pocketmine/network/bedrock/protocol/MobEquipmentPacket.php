<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\bedrock\protocol\types\inventory\ItemInstance;
use pocketmine\network\NetworkSession;

class MobEquipmentPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::MOB_EQUIPMENT_PACKET;

	/** @var int */
	public $actorRuntimeId;
	/** @var ItemInstance */
	public $item;
	/** @var int */
	public $inventorySlot;
	/** @var int */
	public $hotbarSlot;
	/** @var int */
	public $windowId = 0;

	public function decodePayload(){
		$this->actorRuntimeId = $this->getActorRuntimeId();
		$this->item = $this->getItemInstance();
		$this->inventorySlot = $this->getByte();
		$this->hotbarSlot = $this->getByte();
		$this->windowId = $this->getByte();
	}

	public function encodePayload(){
		$this->putActorRuntimeId($this->actorRuntimeId);
		$this->putItemInstance($this->item);
		$this->putByte($this->inventorySlot);
		$this->putByte($this->hotbarSlot);
		$this->putByte($this->windowId);
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleMobEquipment($this);
	}
}
