<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol\types\itemStack;

use pocketmine\network\bedrock\protocol\DataPacket;
use pocketmine\network\bedrock\protocol\ItemStackRequestPacket;

class CraftCreativeStackRequestAction extends StackRequestAction{

	/** @var int */
	protected $creativeItemNetworkId;

	/**
	 * @return int
	 */
	public function getCreativeItemNetworkId() : int{
		return $this->creativeItemNetworkId;
	}

	public function getActionId() : int{
		return ItemStackRequestPacket::ACTION_CRAFT_CREATIVE;
	}

	public function decode(DataPacket $stream) : void{
		$this->creativeItemNetworkId = $stream->getUnsignedVarInt();
	}

	public function encode(DataPacket $stream) : void{
		$stream->putUnsignedVarInt($this->creativeItemNetworkId);
	}
}