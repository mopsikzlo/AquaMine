<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol\types\itemStack;

use pocketmine\network\bedrock\protocol\ItemStackRequestPacket;

class AutoCraftRecipeStackRequestAction extends CraftRecipeStackRequestAction{

	public function getActionId() : int{
		return ItemStackRequestPacket::ACTION_CRAFT_RECIPE_AUTO;
	}
}