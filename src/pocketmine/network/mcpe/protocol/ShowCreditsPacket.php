<?php

declare(strict_types=1);


namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\NetworkSession;

class ShowCreditsPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::SHOW_CREDITS_PACKET;

	public const STATUS_START_CREDITS = 0;
	public const STATUS_END_CREDITS = 1;

	public $playerEid;
	public $status;

	public function decodePayload(){
		$this->playerEid = $this->getEntityRuntimeId();
		$this->status = $this->getVarInt();
	}

	public function encodePayload(){
		$this->putEntityRuntimeId($this->playerEid);
		$this->putVarInt($this->status);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleShowCredits($this);
	}
}