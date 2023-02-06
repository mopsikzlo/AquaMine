<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol\types\itemStack;

use pocketmine\network\bedrock\protocol\DataPacket;
use pocketmine\network\bedrock\protocol\ItemStackRequestPacket;

class LabTableCombineStackRequestAction extends StackRequestAction{

	public function getActionId() : int{
		return ItemStackRequestPacket::ACTION_LAB_TABLE_COMBINE;
	}

	public function decode(DataPacket $stream) : void{

	}

	public function encode(DataPacket $stream) : void{

	}
}