<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\NetworkSession;

class HurtArmorPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::HURT_ARMOR_PACKET;

	/** @var int */
	public $cause;
	/** @var int */
	public $damage;

	public function decodePayload(){
		$this->cause = $this->getVarInt();
		$this->damage = $this->getVarInt();
	}

	public function encodePayload(){
		$this->putVarInt($this->cause);
		$this->putVarInt($this->damage);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleHurtArmor($this);
	}
}
