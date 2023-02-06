<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\adapter\v422\protocol;

use pocketmine\network\bedrock\protocol\types\itemStack\StackResponseSlotInfo;

class ItemStackResponsePacket extends \pocketmine\network\bedrock\protocol\ItemStackResponsePacket{

	protected function getStackResponseSlotInfo() : StackResponseSlotInfo{
		$info = new StackResponseSlotInfo();
		$info->slot = $this->getByte();
		$info->hotbarSlot = $this->getByte();
		$info->count = $this->getByte();
		$info->stackNetworkId = $this->getVarInt();
		$info->customName = $this->getString();
		return $info;
	}

	protected function putStackResponseSlotInfo(StackResponseSlotInfo $info) : void{
		$this->putByte($info->slot);
		$this->putByte($info->hotbarSlot);
		$this->putByte($info->count);
		$this->putVarInt($info->stackNetworkId);
		$this->putString($info->customName);
	}
}