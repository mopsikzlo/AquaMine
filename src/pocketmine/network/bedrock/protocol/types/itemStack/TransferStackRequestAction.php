<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol\types\itemStack;

use pocketmine\network\bedrock\protocol\DataPacket;

abstract class TransferStackRequestAction extends StackRequestAction{

	/** @var int */
	protected $count;
	/** @var StackRequestSlotInfo */
	protected $source;
	/** @var StackRequestSlotInfo */
	protected $destination;

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

	/**
	 * @return StackRequestSlotInfo
	 */
	public function getDestination() : StackRequestSlotInfo{
		return $this->destination;
	}

	public function decode(DataPacket $stream) : void{
		$this->count = $stream->getByte();
		$this->source = $stream->getStackRequestSlotInfo();
		$this->destination = $stream->getStackRequestSlotInfo();
	}

	public function encode(DataPacket $stream) : void{
		$stream->putByte($this->count);
		$stream->putStackRequestSlotInfo($this->source);
		$stream->putStackRequestSlotInfo($this->destination);
	}
}