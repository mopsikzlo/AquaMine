<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\NetworkSession;
use pocketmine\resourcepacks\ResourcePack;
use pocketmine\resourcepacks\ResourcePackInfoEntry;

use function count;

class ResourcePacksInfoPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::RESOURCE_PACKS_INFO_PACKET;

	public $mustAccept = false; //if true, forces client to use selected resource packs
	/** @var ResourcePack[] */
	public $behaviorPackEntries = [];
	/** @var ResourcePack[] */
	public $resourcePackEntries = [];

	public function decodePayload(){
		$this->mustAccept = $this->getBool();
		$behaviorPackCount = $this->getLShort();
		while($behaviorPackCount-- > 0){
			$id = $this->getString();
			$version = $this->getString();
			$size = $this->getLLong();
			$this->behaviorPackEntries[] = new ResourcePackInfoEntry($id, $version, $size);
			$this->getString();
		}

		$resourcePackCount = $this->getLShort();
		while($resourcePackCount-- > 0){
			$id = $this->getString();
			$version = $this->getString();
			$size = $this->getLLong();
			$this->resourcePackEntries[] = new ResourcePackInfoEntry($id, $version, $size);
			$this->getString();
		}
	}

	public function encodePayload(){

		$this->putBool($this->mustAccept);
		$this->putLShort(count($this->behaviorPackEntries));
		foreach($this->behaviorPackEntries as $entry){
			$this->putString($entry->getPackId());
			$this->putString($entry->getPackVersion());
			$this->putLLong($entry->getPackSize());
			$this->putString(""); //TODO
		}
		$this->putLShort(count($this->resourcePackEntries));
		foreach($this->resourcePackEntries as $entry){
			$this->putString($entry->getPackId());
			$this->putString($entry->getPackVersion());
			$this->putLLong($entry->getPackSize());
			$this->putString(""); //TODO
		}
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleResourcePacksInfo($this);
	}
}