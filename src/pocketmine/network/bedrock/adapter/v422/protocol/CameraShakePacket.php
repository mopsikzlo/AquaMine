<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\adapter\v422\protocol;

#include <rules/DataPacket.h>


class CameraShakePacket extends \pocketmine\network\bedrock\protocol\CameraShakePacket{

	public function decodePayload(){
		$this->intensity = $this->getLFloat();
		$this->duration = $this->getLFloat();
		$this->shakeType = $this->getByte();
	}

	public function encodePayload(){
		$this->putLFloat($this->intensity);
		$this->putLFloat($this->duration);
		$this->putByte($this->shakeType);
	}
}