<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\NetworkSession;

class OnScreenTextureAnimationPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::ON_SCREEN_TEXTURE_ANIMATION_PACKET;

	/** @var int */
	public $effectId;

	public function decodePayload(){
		$this->effectId = $this->getLInt();
	}

	public function encodePayload(){
		$this->putLInt($this->effectId);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleOnScreenTextureAnimation($this);
	}
}
