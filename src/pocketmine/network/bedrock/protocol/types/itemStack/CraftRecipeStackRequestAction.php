<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol\types\itemStack;

use pocketmine\network\bedrock\protocol\DataPacket;
use pocketmine\network\bedrock\protocol\ItemStackRequestPacket;

class CraftRecipeStackRequestAction extends StackRequestAction{

	/** @var int */
	protected $recipeNetworkId;

	/**
	 * @return int
	 */
	public function getRecipeNetworkId() : int{
		return $this->recipeNetworkId;
	}

	public function getActionId() : int{
		return ItemStackRequestPacket::ACTION_CRAFT_RECIPE;
	}

	public function decode(DataPacket $stream) : void{
		$this->recipeNetworkId = $stream->getUnsignedVarInt();
	}

	public function encode(DataPacket $stream) : void{
		$stream->putUnsignedVarInt($this->recipeNetworkId);
	}
}