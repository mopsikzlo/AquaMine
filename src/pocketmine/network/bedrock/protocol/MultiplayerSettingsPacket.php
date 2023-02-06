<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\NetworkSession;

class MultiplayerSettingsPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::MULTIPLAYER_SETTINGS_PACKET;

	public const MODE_ENABLE = 0;
	public const MODE_DISABLE = 1;
	public const MODE_JOIN_CODE = 2;

	/** @var int */
	public $mode;

	public function decodePayload(){
		$this->mode = $this->getVarInt();
	}

	public function encodePayload(){
		$this->putVarInt($this->mode);
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleMultiplayerSettings($this);
	}
}