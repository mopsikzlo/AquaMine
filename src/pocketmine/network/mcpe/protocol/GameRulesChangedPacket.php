<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\NetworkSession;

class GameRulesChangedPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::GAME_RULES_CHANGED_PACKET;

	public $gameRules = [];

	public function decodePayload(){
		$this->gameRules = $this->getGameRules();
	}

	public function encodePayload(){
		$this->putGameRules($this->gameRules);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleGameRulesChanged($this);
	}

}