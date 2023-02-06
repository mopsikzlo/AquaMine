<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>


use pocketmine\item\Item;
use pocketmine\network\bedrock\protocol\types\inventory\ItemInstance;
use pocketmine\network\NetworkSession;

class MobArmorEquipmentPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::MOB_ARMOR_EQUIPMENT_PACKET;

	/** @var int */
	public $actorRuntimeId;
	/** @var ItemInstance[] */
	public $slots = [];

	public function decodePayload(){
		$this->actorRuntimeId = $this->getActorRuntimeId();
		for($i = 0; $i < 4; ++$i){
			$this->slots[$i] = $this->getItemInstance();
		}
	}

	public function encodePayload(){
		$this->putActorRuntimeId($this->actorRuntimeId);
		for($i = 0; $i < 4; ++$i){
			$this->putItemInstance($this->slots[$i]);
		}
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleMobArmorEquipment($this);
	}
}
