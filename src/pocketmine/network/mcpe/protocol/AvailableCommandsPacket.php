<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>

use InvalidArgumentException;
use pocketmine\network\NetworkSession;
use function gettype;
use function is_array;

class AvailableCommandsPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::AVAILABLE_COMMANDS_PACKET;

	/** @var array */
	public $commandData;
	/** @var string */
	public $unknown = "";

	public function decodePayload(){
		$this->commandData = $this->getJson();
		if(!is_array($this->commands)){
			throw new InvalidArgumentException("Commands expected to be array, got " . gettype($this->commands));
		}
		$this->unknown = $this->getString();
	}

	public function encodePayload(){
		$this->putJson($this->commandData);
		$this->putString($this->unknown);
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleAvailableCommands($this);
	}

}