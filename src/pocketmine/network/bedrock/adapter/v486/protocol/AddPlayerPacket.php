<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\adapter\v486\protocol;

#include <rules/DataPacket.h>


use function count;

class AddPlayerPacket extends \pocketmine\network\bedrock\protocol\AddPlayerPacket{
	use PacketTrait;

	public function decodePayload(){
		$this->uuid = $this->getUUID();
		$this->username = $this->getString();
		$this->actorUniqueId = $this->getActorUniqueId();
		$this->actorRuntimeId = $this->getActorRuntimeId();
		$this->platformChatId = $this->getString();
		$this->position = $this->getVector3();
		$this->motion = $this->getVector3();
		$this->pitch = $this->getLFloat();
		$this->yaw = $this->getLFloat();
		$this->headYaw = $this->getLFloat();
		$this->item = $this->getItemInstance();
		$this->metadata = $this->getActorMetadata();

		$this->uvarint1 = $this->getUnsignedVarInt();
		$this->uvarint2 = $this->getUnsignedVarInt();
		$this->uvarint3 = $this->getUnsignedVarInt();
		$this->uvarint4 = $this->getUnsignedVarInt();
		$this->uvarint5 = $this->getUnsignedVarInt();

		$this->long1 = $this->getLLong();

		$linkCount = $this->getUnsignedVarInt();
		for($i = 0; $i < $linkCount; ++$i){
			$this->links[$i] = $this->getActorLink();
		}

		$this->deviceId = $this->getString();
		$this->deviceOS = $this->getLInt();
	}

	public function encodePayload(){
		$this->putUUID($this->uuid);
		$this->putString($this->username);
		$this->putActorUniqueId($this->actorUniqueId ?? $this->actorRuntimeId);
		$this->putActorRuntimeId($this->actorRuntimeId);
		$this->putString($this->platformChatId);
		$this->putVector3($this->position);
		$this->putVector3Nullable($this->motion);
		$this->putLFloat($this->pitch);
		$this->putLFloat($this->yaw);
		$this->putLFloat($this->headYaw ?? $this->yaw);
		$this->putItemInstance($this->item);
		$this->putActorMetadata($this->metadata);

		$this->putUnsignedVarInt($this->uvarint1);
		$this->putUnsignedVarInt($this->uvarint2);
		$this->putUnsignedVarInt($this->uvarint3);
		$this->putUnsignedVarInt($this->uvarint4);
		$this->putUnsignedVarInt($this->uvarint5);

		$this->putLLong($this->long1);

		$this->putUnsignedVarInt(count($this->links));
		foreach($this->links as $link){
			$this->putActorLink($link);
		}

		$this->putString($this->deviceId);
		$this->putLInt($this->deviceOS);
	}
}
