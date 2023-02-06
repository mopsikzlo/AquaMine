<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol\types\itemStack;

use pocketmine\network\bedrock\protocol\DataPacket;
use pocketmine\network\bedrock\protocol\ItemStackRequestPacket;

class DestroyStackRequestAction extends StackRequestAction{

	/** @var int */
	protected $count;
	/** @var StackRequestSlotInfo */
	protected $source;

	/**
	 * @return int
	 */
	public function getCount() : int{
		return $this->count;
	}

	/**
	 * @return StackRequestSlotInfo
	 */
	public function getSource() : StackRequestSlotInfo{
		return $this->source;
	}

	public function getActionId() : int{
		return ItemStackRequestPacket::ACTION_DESTROY;
	}

	public function decode(DataPacket $stream) : void{
		$this->count = $stream->getByte();
		$this->source = $stream->getStackRequestSlotInfo();
	}

	public function encode(DataPacket $stream) : void{
		$stream->putByte($this->count);
		$stream->putStackRequestSlotInfo($this->source);
	}
}