<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\NetworkSession;

class NetworkSettingsPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::NETWORK_SETTINGS_PACKET;

	/** @var int */
	public $networkSettingOptions; // (???)

	public function decodePayload(){
		$this->networkSettingOptions = $this->getLShort();
	}

	public function encodePayload(){
		$this->putLShort($this->networkSettingOptions);
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleNetworkSettings($session);
	}
}