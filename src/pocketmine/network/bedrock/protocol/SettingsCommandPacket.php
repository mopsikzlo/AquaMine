<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\NetworkSession;

class SettingsCommandPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::SETTINGS_COMMAND_PACKET;

	/** @var string */
	public $commandString;
	/** @var bool */
	public $supressOutput;

	public function decodePayload(){
		$this->commandString = $this->getString();
		$this->supressOutput = $this->getBool();
	}

	public function encodePayload(){
		$this->putString($this->commandString);
		$this->putBool($this->supressOutput);
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleSettingsCommand($this);
	}
}