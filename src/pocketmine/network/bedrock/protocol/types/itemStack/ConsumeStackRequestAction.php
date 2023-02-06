<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol\types\itemStack;

use pocketmine\network\bedrock\protocol\ItemStackRequestPacket;

class ConsumeStackRequestAction extends DestroyStackRequestAction{

	public function getActionId() : int{
		return ItemStackRequestPacket::ACTION_CONSUME;
	}
}