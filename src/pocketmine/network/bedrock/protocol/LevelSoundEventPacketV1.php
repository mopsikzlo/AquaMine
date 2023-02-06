<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>

use pocketmine\math\Vector3;
use pocketmine\network\NetworkSession;

/**
 * Useless leftover from a 1.8 refactor, does nothing
 */
class LevelSoundEventPacketV1 extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::LEVEL_SOUND_EVENT_PACKET_V1;

	/** @var int */
	public $sound;
	/** @var Vector3 */
	public $position;
	/** @var int */
	public $extraData = 0;
	/** @var int */
	public $actorType = 1;
	/** @var bool */
	public $isBabyMob = false; //...
	/** @var bool */
	public $disableRelativeVolume = false;

	public function decodePayload(){
		$this->sound = $this->getByte();
		$this->position = $this->getVector3();
		$this->extraData = $this->getVarInt();
		$this->actorType = $this->getVarInt();
		$this->isBabyMob = $this->getBool();
		$this->disableRelativeVolume = $this->getBool();
	}

	public function encodePayload(){
		$this->putByte($this->sound);
		$this->putVector3($this->position);
		$this->putVarInt($this->extraData);
		$this->putVarInt($this->actorType);
		$this->putBool($this->isBabyMob);
		$this->putBool($this->disableRelativeVolume);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleLevelSoundEventPacketV1($this);
	}
}
