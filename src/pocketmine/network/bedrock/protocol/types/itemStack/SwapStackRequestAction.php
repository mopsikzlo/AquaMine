<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol\types\itemStack;

use pocketmine\network\bedrock\protocol\DataPacket;
use pocketmine\network\bedrock\protocol\ItemStackRequestPacket;

class SwapStackRequestAction extends TransferStackRequestAction{

	/** @var StackRequestSlotInfo */
	protected $source;
	/** @var StackRequestSlotInfo */
	protected $destination;

	/**
	 * @return StackRequestSlotInfo
	 */
	public function getSource() : StackRequestSlotInfo{
		return $this->source;
	}

	/**
	 * @return StackRequestSlotInfo
	 */
	public function getDestination() : StackRequestSlotInfo{
		return $this->destination;
	}

	public function getActionId() : int{
		return ItemStackRequestPacket::ACTION_SWAP;
	}

	public function decode(DataPacket $stream) : void{
		$this->source = $stream->getStackRequestSlotInfo();
		$this->destination = $stream->getStackRequestSlotInfo();
	}

	public function encode(DataPacket $stream) : void{
		$stream->putStackRequestSlotInfo($this->source);
		$stream->putStackRequestSlotInfo($this->destination);
	}
}