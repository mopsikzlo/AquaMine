<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol\types\itemStack;

use pocketmine\network\bedrock\protocol\DataPacket;
use pocketmine\network\bedrock\protocol\ItemStackRequestPacket;

class CreateStackRequestAction extends StackRequestAction{

	/** @var int */
	protected $resultsSlot;

	/**
	 * @return int
	 */
	public function getResultsSlot() : int{
		return $this->resultsSlot;
	}

	public function getActionId() : int{
		return ItemStackRequestPacket::ACTION_CREATE;
	}

	public function decode(DataPacket $stream) : void{
		$this->resultsSlot = $stream->getByte();
	}

	public function encode(DataPacket $stream) : void{
		$stream->putByte($this->resultsSlot);
	}
}