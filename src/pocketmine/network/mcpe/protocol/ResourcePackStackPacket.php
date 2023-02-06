<?php

declare(strict_types=1);


namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\NetworkSession;
use pocketmine\resourcepacks\ResourcePack;
use pocketmine\resourcepacks\ResourcePackInfoEntry;

use function count;

class ResourcePackStackPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::RESOURCE_PACK_STACK_PACKET;

	public $mustAccept = false;

	/** @var ResourcePack[] */
	public $behaviorPackStack = [];
	/** @var ResourcePack[] */
	public $resourcePackStack = [];

	public function decodePayload(){
		$this->mustAccept = $this->getBool();
		$behaviorPackCount = $this->getUnsignedVarInt();
		while($behaviorPackCount-- > 0){
			$packId = $this->getString();
			$version = $this->getString();
			$this->behaviorPackStack[] = new ResourcePackInfoEntry($packId, $version);
		}

		$resourcePackCount = $this->getUnsignedVarInt();
		while($resourcePackCount-- > 0){
			$packId = $this->getString();
			$version = $this->getString();
			$this->resourcePackStack[] = new ResourcePackInfoEntry($packId, $version);
		}
	}

	public function encodePayload(){
		$this->putBool($this->mustAccept);

		$this->putUnsignedVarInt(count($this->behaviorPackStack));
		foreach($this->behaviorPackStack as $entry){
			$this->putString($entry->getPackId());
			$this->putString($entry->getPackVersion());
		}

		$this->putUnsignedVarInt(count($this->resourcePackStack));
		foreach($this->resourcePackStack as $entry){
			$this->putString($entry->getPackId());
			$this->putString($entry->getPackVersion());
		}
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleResourcePackStack($this);
	}
}