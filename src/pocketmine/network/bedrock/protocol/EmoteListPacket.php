<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\NetworkSession;
use pocketmine\utils\UUID;
use function count;

class EmoteListPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::EMOTE_LIST_PACKET;

	/** @var int */
	public $playerRuntimeId;
	/** @var UUID[] */
	public $emotePieces = [];

	public function decodePayload(){
		$this->playerRuntimeId = $this->getActorRuntimeId();
		for($i = 0, $count = $this->getUnsignedVarInt(); $i < $count; ++$i){
			$this->emotePieces[] = $this->getUUID();
		}
	}

	public function encodePayload(){
		$this->putActorRuntimeId($this->playerRuntimeId);
		$this->putUnsignedVarInt(count($this->emotePieces));
		foreach($this->emotePieces as $uuid){
			$this->putUUID($uuid);
		}
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleEmoteList($this);
	}
}