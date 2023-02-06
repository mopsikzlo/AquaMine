<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\bedrock\protocol\types\DimensionIds;
use pocketmine\network\NetworkSession;

class SetSpawnPositionPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::SET_SPAWN_POSITION_PACKET;

	public const TYPE_PLAYER_SPAWN = 0;
	public const TYPE_WORLD_SPAWN = 1;

	/** @var int */
	public $spawnType;
	/** @var int */
	public $x, $y, $z;
	/** @var int */
	public $dimension = DimensionIds::OVERWORLD;
	/** @var int */
	public $spawnX, $spawnY, $spawnZ;

	public function decodePayload(){
		$this->spawnType = $this->getVarInt();
		$this->getBlockPosition($this->x, $this->y, $this->z);
		$this->dimension = $this->getVarInt();
		$this->getBlockPosition($this->spawnX, $this->spawnY, $this->spawnZ);
	}

	public function encodePayload(){
		$this->putVarInt($this->spawnType);
		$this->putBlockPosition($this->x, $this->y, $this->z);
		$this->putVarInt($this->dimension);
		$this->putBlockPosition($this->spawnX, $this->spawnY, $this->spawnZ);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleSetSpawnPosition($this);
	}
}
