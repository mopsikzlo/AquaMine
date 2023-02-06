<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\adapter\v503\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\bedrock\protocol\types\PlayMode;

class PlayerAuthInputPacket extends \pocketmine\network\bedrock\protocol\PlayerAuthInputPacket{

	public function decodePayload(){
		$this->yaw = $this->getLFloat();
		$this->pitch = $this->getLFloat();
		$this->playerMovePosition = $this->getVector3();
		$this->motion = $this->getVector2();
		$this->headRotation = $this->getLFloat();
		$this->inputFlags = $this->getUnsignedVarLong();
		$this->inputMode = $this->getUnsignedVarInt();
		$this->playMode = $this->getUnsignedVarInt();
		if($this->playMode === PlayMode::VR){
			$this->vrGazeDirection = $this->getVector3();
		}
		$this->tick = $this->getUnsignedVarLong();
		$this->delta = $this->getVector3();

		$this->interactionMode = self::INTERACTION_CLASSIC;
	}

	public function encodePayload(){
		$this->putLFloat($this->yaw);
		$this->putLFloat($this->pitch);
		$this->putVector3($this->playerMovePosition);
		$this->putVector2($this->motion);
		$this->putLFloat($this->headRotation);
		$this->putUnsignedVarLong($this->inputFlags);
		$this->putUnsignedVarInt($this->inputMode);
		$this->putUnsignedVarInt($this->playMode);
		if($this->playMode === PlayMode::VR){
			$this->putVector3($this->vrGazeDirection);
		}
		$this->putUnsignedVarLong($this->tick);
		$this->putVector3($this->delta);
	}
}
