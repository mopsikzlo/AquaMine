<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol\types\itemStack;

use pocketmine\network\bedrock\protocol\DataPacket;
use pocketmine\network\bedrock\protocol\ItemStackRequestPacket;

class CraftNonImplementedStackRequestAction extends StackRequestAction{

	public function getActionId() : int{
		return ItemStackRequestPacket::ACTION_CRAFT_NON_IMPLEMENTED_DEPRECATED;
	}

	public function decode(DataPacket $stream) : void{

	}

	public function encode(DataPacket $stream) : void{

	}
}