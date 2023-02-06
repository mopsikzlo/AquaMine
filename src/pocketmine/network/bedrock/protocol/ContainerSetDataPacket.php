<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\NetworkSession;

class ContainerSetDataPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::CONTAINER_SET_DATA_PACKET;

	public const PROPERTY_FURNACE_TICK_COUNT = 0;
	public const PROPERTY_FURNACE_LIT_TIME = 1;
	public const PROPERTY_FURNACE_LIT_DURATION = 2;
	//TODO: check property 3
	public const PROPERTY_FURNACE_FUEL_AUX = 4;

	public const PROPERTY_BREWING_STAND_BREW_TIME = 0;
	public const PROPERTY_BREWING_STAND_FUEL_AMOUNT = 1;
	public const PROPERTY_BREWING_STAND_FUEL_TOTAL = 2;

	/** @var int */
	public $windowId;
	/** @var int */
	public $property;
	/** @var int */
	public $value;

	public function decodePayload(){
		$this->windowId = $this->getByte();
		$this->property = $this->getVarInt();
		$this->value = $this->getVarInt();
	}

	public function encodePayload(){
		$this->putByte($this->windowId);
		$this->putVarInt($this->property);
		$this->putVarInt($this->value);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleContainerSetData($this);
	}
}
