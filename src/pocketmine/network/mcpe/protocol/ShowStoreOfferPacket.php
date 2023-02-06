<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\NetworkSession;

class ShowStoreOfferPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::SHOW_STORE_OFFER_PACKET;

	public $offerId;

	public function decodePayload(){
		$this->offerId = $this->getString();
	}

	public function encodePayload(){
		$this->putString($this->offerId);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleShowStoreOffer($this);
	}
}