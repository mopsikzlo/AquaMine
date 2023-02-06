<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\NetworkSession;

class UpdateEquipPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::UPDATE_EQUIP_PACKET;

	public $windowId;
	public $windowType;
	public $unknownVarint; //TODO: find out what this is (vanilla always sends 0)
	public $entityUniqueId;
	public $namedtag;

	public function decodePayload(){
		$this->windowId = $this->getByte();
		$this->windowType = $this->getByte();
		$this->unknownVarint = $this->getVarInt();
		$this->entityUniqueId = $this->getEntityUniqueId();
		$this->namedtag = $this->getRemaining();
	}

	public function encodePayload(){
		$this->putByte($this->windowId);
		$this->putByte($this->windowType);
		$this->putVarInt($this->unknownVarint);
		$this->putEntityUniqueId($this->entityUniqueId);
		$this->put($this->namedtag);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleUpdateEquip($this);
	}
}