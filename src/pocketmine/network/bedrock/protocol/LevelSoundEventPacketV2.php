<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>

use pocketmine\math\Vector3;
use pocketmine\network\NetworkSession;

/**
 * Useless leftover from a 1.9 refactor, does nothing
 */
class LevelSoundEventPacketV2 extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::LEVEL_SOUND_EVENT_PACKET_V2;

	/** @var int */
	public $sound;
	/** @var Vector3 */
	public $position;
	/** @var int */
	public $extraData = -1;
	/** @var string */
	public $actorType = ":"; //???
	/** @var bool */
	public $isBabyMob = false; //...
	/** @var bool */
	public $disableRelativeVolume = false;

	public function decodePayload(){
		$this->sound = $this->getByte();
		$this->position = $this->getVector3();
		$this->extraData = $this->getVarInt();
		$this->actorType = $this->getString();
		$this->isBabyMob = $this->getBool();
		$this->disableRelativeVolume = $this->getBool();
	}

	public function encodePayload(){
		$this->putByte($this->sound);
		$this->putVector3($this->position);
		$this->putVarInt($this->extraData);
		$this->putString($this->actorType);
		$this->putBool($this->isBabyMob);
		$this->putBool($this->disableRelativeVolume);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleLevelSoundEventPacketV2($this);
	}
}
