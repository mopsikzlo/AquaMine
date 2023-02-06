<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\NetworkSession;
use pocketmine\network\bedrock\protocol\types\ScoreboardIdentityPacketEntry;
use function count;

class SetScoreboardIdentityPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::SET_SCOREBOARD_IDENTITY_PACKET;

	public const TYPE_REGISTER_IDENTITY = 0;
	public const TYPE_CLEAR_IDENTITY = 1;

	/** @var int */
	public $type;
	/** @var ScoreboardIdentityPacketEntry[] */
	public $entries = [];

	public function decodePayload(){
		$this->type = $this->getByte();
		for($i = 0, $count = $this->getUnsignedVarInt(); $i < $count; ++$i){
			$entry = new ScoreboardIdentityPacketEntry();
			$entry->scoreboardId = $this->getVarLong();
			if($this->type === self::TYPE_REGISTER_IDENTITY){
				$entry->actorUniqueId = $this->getActorUniqueId();
			}

			$this->entries[] = $entry;
		}
	}

	public function encodePayload(){
		$this->putByte($this->type);
		$this->putUnsignedVarInt(count($this->entries));
		foreach($this->entries as $entry){
			$this->putVarLong($entry->scoreboardId);
			if($this->type === self::TYPE_REGISTER_IDENTITY){
				$this->putActorUniqueId($entry->actorUniqueId);
			}
		}
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleSetScoreboardIdentity($this);
	}
}
