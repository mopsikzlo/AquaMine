<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\NetworkSession;
use pocketmine\network\bedrock\protocol\types\CommandOriginData;

class CommandRequestPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::COMMAND_REQUEST_PACKET;

	/** @var string */
	public $command;
	/** @var CommandOriginData */
	public $originData;
	/** @var bool */
	public $isInternal;

	public function decodePayload(){
		$this->command = $this->getString();
		$this->originData = $this->getCommandOriginData();
		$this->isInternal = $this->getBool();
	}

	public function encodePayload(){
		$this->putString($this->command);
		$this->putCommandOriginData($this->originData);
		$this->putBool($this->isInternal);
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleCommandRequest($this);
	}
}
