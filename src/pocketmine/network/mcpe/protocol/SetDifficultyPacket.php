<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\NetworkSession;

class SetDifficultyPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::SET_DIFFICULTY_PACKET;

	public $difficulty;

	public function decodePayload(){
		$this->difficulty = $this->getUnsignedVarInt();
	}

	public function encodePayload(){
		$this->putUnsignedVarInt($this->difficulty);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleSetDifficulty($this);
	}

}