<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\NetworkSession;

class AnimatePacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::ANIMATE_PACKET;

	public const ACTION_SWING_ARM = 1;

	public const ACTION_STOP_SLEEP = 3;
	public const ACTION_CRITICAL_HIT = 4;

	public $action;
	public $entityRuntimeId;
	public $float = 0.0; //TODO (Boat rowing time?)

	public function decodePayload(){
		$this->action = $this->getVarInt();
		$this->entityRuntimeId = $this->getEntityRuntimeId();
		if($this->action & 0x80){
			$this->float = $this->getLFloat();
		}
	}

	public function encodePayload(){
		$this->putVarInt($this->action);
		$this->putEntityRuntimeId($this->entityRuntimeId);
		if($this->action & 0x80){
			$this->putLFloat($this->float);
		}
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleAnimate($this);
	}

}
