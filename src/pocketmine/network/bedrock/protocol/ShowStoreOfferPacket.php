<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\NetworkSession;

class ShowStoreOfferPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::SHOW_STORE_OFFER_PACKET;

	/** @var string */
	public $offerId;
	/** @var bool */
	public $showAll;

	public function decodePayload(){
		$this->offerId = $this->getString();
		$this->showAll = $this->getBool();
	}

	public function encodePayload(){
		$this->putString($this->offerId);
		$this->putBool($this->showAll);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleShowStoreOffer($this);
	}
}
