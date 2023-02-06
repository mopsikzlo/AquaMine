<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\NetworkSession;

class FilterTextPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::FILTER_TEXT_PACKET;

	/** @var string */
	public $text;
	/** @var bool */
	public $fromServer;

	public function decodePayload(){
		$this->text = $this->getString();
		$this->fromServer = $this->getBool();
	}

	public function encodePayload(){
		$this->putString($this->text);
		$this->putBool($this->fromServer);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleFilterText($this);
	}
}
