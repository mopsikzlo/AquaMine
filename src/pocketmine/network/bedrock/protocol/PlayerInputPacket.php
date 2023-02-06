<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\NetworkSession;

class PlayerInputPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::PLAYER_INPUT_PACKET;

	/** @var float */
	public $motionX;
	/** @var float */
	public $motionY;
	/** @var bool */
	public $jumping;
	/** @var bool */
	public $sneaking;

	public function decodePayload(){
		$this->motionX = $this->getLFloat();
		$this->motionY = $this->getLFloat();
		$this->jumping = $this->getBool();
		$this->sneaking = $this->getBool();
	}

	public function encodePayload(){
		$this->putLFloat($this->motionX);
		$this->putLFloat($this->motionY);
		$this->putBool($this->jumping);
		$this->putBool($this->sneaking);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handlePlayerInput($this);
	}
}
