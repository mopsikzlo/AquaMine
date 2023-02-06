<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\adapter\v428\protocol;

use pocketmine\network\bedrock\protocol\types\inventory\ItemInstance;

trait InventoryPacketTrait{
	use PacketTrait;

	public function getItemInstance() : ItemInstance{
		$inst = new ItemInstance();
		$inst->stackNetworkId = $this->getVarInt();
		$inst->stack = $this->getItemStackWithoutStackId();
		return $inst;
	}

	public function putItemInstance($instance) : void{
		assert($instance instanceof ItemInstance);

		$this->putVarInt($instance->stackNetworkId);
		$this->putItemStackWithoutStackId($instance->stack);
	}
}