<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol\types\itemStack;

use pocketmine\network\bedrock\protocol\DataPacket;
use pocketmine\network\bedrock\protocol\ItemStackRequestPacket;

class BeaconPaymentStackRequestAction extends StackRequestAction{

	/** @var int */
	protected $primaryEffect;
	/** @var int */
	protected $secondaryEffect;

	/**
	 * @return int
	 */
	public function getPrimaryEffect() : int{
		return $this->primaryEffect;
	}

	/**
	 * @return int
	 */
	public function getSecondaryEffect() : int{
		return $this->secondaryEffect;
	}

	public function getActionId() : int{
		return ItemStackRequestPacket::ACTION_BEACON_PAYMENT;
	}

	public function decode(DataPacket $stream) : void{
		$this->primaryEffect = $stream->getVarInt();
		$this->secondaryEffect = $stream->getVarInt();
	}

	public function encode(DataPacket $stream) : void{
		$stream->putVarInt($this->primaryEffect);
		$stream->putVarInt($this->secondaryEffect);
	}
}