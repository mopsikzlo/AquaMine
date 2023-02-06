<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\NetworkSession;

class AnvilDamagePacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::ANVIL_DAMAGE_PACKET;

	/** @var int */
	public $anvilDamageState;
	/** @var int */
	public $x;
	/** @var int */
	public $y;
	/** @var int */
	public $z;

	public function decodePayload() : void{
		$this->anvilDamageState = $this->getByte();
		$this->getBlockPosition($this->x, $this->y, $this->z);
	}

	public function encodePayload() : void{
		$this->putByte($this->anvilDamageState);
		$this->putBlockPosition($this->x, $this->y, $this->z);
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleAnvilDamage($this);
	}
}