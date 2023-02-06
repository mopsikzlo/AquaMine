<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\adapter\v407\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\bedrock\protocol\types\Experiments;
use function count;

class ResourcePackStackPacket extends \pocketmine\network\bedrock\protocol\ResourcePackStackPacket{
	use PacketTrait;

	public function decodePayload(){
		$this->mustAccept = $this->getBool();
		$behaviorPackCount = $this->getUnsignedVarInt();
		while($behaviorPackCount-- > 0){
			$this->getString();
			$this->getString();
			$this->getString();
		}

		$resourcePackCount = $this->getUnsignedVarInt();
		while($resourcePackCount-- > 0){
			$this->getString();
			$this->getString();
			$this->getString();
		}

		$this->experiments = new Experiments([], $this->getBool());
		$this->gameVersion = $this->getString();
	}

	public function encodePayload(){
		$this->putBool($this->mustAccept);

		$this->putUnsignedVarInt(count($this->behaviorPackStack));
		foreach($this->behaviorPackStack as $entry){
			$this->putString($entry->getPackId());
			$this->putString($entry->getPackVersion());
			$this->putString(""); //TODO: subpack name
		}

		$this->putUnsignedVarInt(count($this->resourcePackStack));
		foreach($this->resourcePackStack as $entry){
			$this->putString($entry->getPackId());
			$this->putString($entry->getPackVersion());
			$this->putString(""); //TODO: subpack name
		}

		$this->putBool(count($this->experiments->experiments) > 0);
		$this->putString($this->gameVersion);
	}
}