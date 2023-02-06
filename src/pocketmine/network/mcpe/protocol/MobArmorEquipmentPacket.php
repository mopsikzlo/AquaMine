<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>


use pocketmine\item\Item;
use pocketmine\network\NetworkSession;

class MobArmorEquipmentPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::MOB_ARMOR_EQUIPMENT_PACKET;

	public $entityRuntimeId;
	/** @var Item[] */
	public $slots = [];

	public function decodePayload(){
		$this->entityRuntimeId = $this->getEntityRuntimeId();
		$this->slots[0] = $this->getSlot();
		$this->slots[1] = $this->getSlot();
		$this->slots[2] = $this->getSlot();
		$this->slots[3] = $this->getSlot();
	}

	public function encodePayload(){
		$this->putEntityRuntimeId($this->entityRuntimeId);
		$this->putSlot($this->slots[0]);
		$this->putSlot($this->slots[1]);
		$this->putSlot($this->slots[2]);
		$this->putSlot($this->slots[3]);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleMobArmorEquipment($this);
	}

}
