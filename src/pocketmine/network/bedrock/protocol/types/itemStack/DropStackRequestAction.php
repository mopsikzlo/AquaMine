<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol\types\itemStack;

use pocketmine\network\bedrock\protocol\DataPacket;
use pocketmine\network\bedrock\protocol\ItemStackRequestPacket;

class DropStackRequestAction extends StackRequestAction{

	/** @var int */
	protected $count;
	/** @var StackRequestSlotInfo */
	protected $source;
	/** @var bool */
	protected $randomly;

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
	 * @return bool
	 */
	public function isRandomly() : bool{
		return $this->randomly;
	}

	public function getActionId() : int{
		return ItemStackRequestPacket::ACTION_DROP;
	}

	public function decode(DataPacket $stream) : void{
		$this->count = $stream->getByte();
		$this->source = $stream->getStackRequestSlotInfo();
		$this->randomly = $stream->getBool();
	}

	public function encode(DataPacket $stream) : void{
		$stream->putByte($this->count);
		$stream->putStackRequestSlotInfo($this->source);
		$stream->putBool($this->randomly);
	}
}