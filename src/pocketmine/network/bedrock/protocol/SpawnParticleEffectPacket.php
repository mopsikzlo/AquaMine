<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>

use pocketmine\math\Vector3;
use pocketmine\network\NetworkSession;
use pocketmine\network\bedrock\protocol\types\DimensionIds;

class SpawnParticleEffectPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::SPAWN_PARTICLE_EFFECT_PACKET;

	/** @var int */
	public $dimensionId = DimensionIds::OVERWORLD; //wtf mojang
	/** @var int */
	public $actorUniqueId = -1;
	/** @var Vector3 */
	public $position;
	/** @var string */
	public $particleName;
	/** @var string */
	public $molangVariablesJson = "";

	public function decodePayload(){
		$this->dimensionId = $this->getByte();
		$this->actorUniqueId = $this->getActorUniqueId();
		$this->position = $this->getVector3();
		$this->particleName = $this->getString();
		$this->molangVariablesJson = $this->getString();
	}

	public function encodePayload(){
		$this->putByte($this->dimensionId);
		$this->putActorUniqueId($this->actorUniqueId);
		$this->putVector3($this->position);
		$this->putString($this->particleName);
		$this->putString($this->molangVariablesJson);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleSpawnParticleEffect($this);
	}
}