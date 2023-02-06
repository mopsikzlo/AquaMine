<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\NetworkSession;

class CodeBuilderPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::CODE_BUILDER_PACKET;

	/** @var string */
	public $url;
	/** @var bool */
	public $shouldOpenCodeBuilder;

	public function decodePayload(){
		$this->url = $this->getString();
		$this->shouldOpenCodeBuilder = $this->getBool();
	}

	public function encodePayload(){
		$this->putString($this->url);
		$this->putBool($this->shouldOpenCodeBuilder);
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleCodeBuilder($this);
	}
}