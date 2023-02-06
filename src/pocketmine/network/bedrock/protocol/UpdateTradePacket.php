<?php

declare(strict_types=1);


namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\NetworkSession;
use pocketmine\network\bedrock\protocol\types\inventory\WindowTypes;

class UpdateTradePacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::UPDATE_TRADE_PACKET;

	//TODO: find fields

	/** @var int */
	public $windowId;
	/** @var int */
	public $windowType = WindowTypes::TRADING; //Mojang hardcoded this -_-
	/** @var int */
	public $unknownVarInt1; //hardcoded to 0
	/** @var int */
	public $tradeTier;
	/** @var int */
	public $traderUniqueId;
	/** @var int */
	public $playerUniqueId;
	/** @var string */
	public $displayName;
	/** @var bool */
	public $screen2;
	/** @var bool */
	public $isWilling;
	/** @var string */
	public $offers;

	public function decodePayload(){
		$this->windowId = $this->getByte();
		$this->windowType = $this->getByte();
		$this->unknownVarInt1 = $this->getVarInt();
		$this->tradeTier = $this->getVarInt();
		$this->traderUniqueId = $this->getActorUniqueId();
		$this->playerUniqueId = $this->getActorUniqueId();
		$this->displayName = $this->getString();
		$this->screen2 = $this->getBool();
		$this->isWilling = $this->getBool();
		$this->offers = $this->getRemaining();
	}

	public function encodePayload(){
		$this->putByte($this->windowId);
		$this->putByte($this->windowType);
		$this->putVarInt($this->unknownVarInt1);
		$this->putVarInt($this->tradeTier);
		$this->putActorUniqueId($this->traderUniqueId);
		$this->putActorUniqueId($this->playerUniqueId);
		$this->putString($this->displayName);
		$this->putBool($this->screen2);
		$this->putBool($this->isWilling);
		$this->put($this->offers);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleUpdateTrade($this);
	}
}
