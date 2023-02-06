<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\NetworkSession;
use pocketmine\network\bedrock\protocol\types\PlayerPermissions;

class AdventureSettingsPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::ADVENTURE_SETTINGS_PACKET;

	public const PERMISSION_NORMAL = 0;
	public const PERMISSION_OPERATOR = 1;
	public const PERMISSION_HOST = 2;
	public const PERMISSION_AUTOMATION = 3;
	public const PERMISSION_ADMIN = 4;

	/**
	 * This constant is used to identify flags that should be set on the second field. In a sensible world, these
	 * flags would all be set on the same packet field, but as of MCPE 1.2, the new abilities flags have for some
	 * reason been assigned a separate field.
	 */
	public const BITFLAG_SECOND_SET = 1 << 16;

	public const WORLD_IMMUTABLE = 0x01;
	public const NO_PVP = 0x02;

	public const AUTO_JUMP = 0x20;
	public const ALLOW_FLIGHT = 0x40;
	public const NO_CLIP = 0x80;
	public const WORLD_BUILDER = 0x100;
	public const FLYING = 0x200;
	public const MUTED = 0x400;

	public const MINE = 0x01 | self::BITFLAG_SECOND_SET;
	public const DOORS_AND_SWITCHES = 0x02 | self::BITFLAG_SECOND_SET;
	public const OPEN_CONTAINERS = 0x04 | self::BITFLAG_SECOND_SET;
	public const ATTACK_PLAYERS = 0x08 | self::BITFLAG_SECOND_SET;
	public const ATTACK_MOBS = 0x10 | self::BITFLAG_SECOND_SET;
	public const OPERATOR = 0x20 | self::BITFLAG_SECOND_SET;
	public const TELEPORT = 0x80 | self::BITFLAG_SECOND_SET;
	public const BUILD = 0x100 | self::BITFLAG_SECOND_SET;
	public const DEFAULT = 0x200 | self::BITFLAG_SECOND_SET;

	/** @var int */
	public $flags = 0;
	/** @var int */
	public $commandPermission = self::PERMISSION_NORMAL;
	/** @var int */
	public $flags2 = -1;
	/** @var int */
	public $playerPermission = PlayerPermissions::MEMBER;
	/** @var int */
	public $customFlags = 0; //...
	/** @var int */
	public $actorUniqueId; //This is a little-endian long, NOT a var-long. (WTF Mojang)

	public function decodePayload(){
		$this->flags = $this->getUnsignedVarInt();
		$this->commandPermission = $this->getUnsignedVarInt();
		$this->flags2 = $this->getUnsignedVarInt();
		$this->playerPermission = $this->getUnsignedVarInt();
		$this->customFlags = $this->getUnsignedVarInt();
		$this->actorUniqueId = $this->getLLong();
	}

	public function encodePayload(){
		$this->putUnsignedVarInt($this->flags);
		$this->putUnsignedVarInt($this->commandPermission);
		$this->putUnsignedVarInt($this->flags2);
		$this->putUnsignedVarInt($this->playerPermission);
		$this->putUnsignedVarInt($this->customFlags);
		$this->putLLong($this->actorUniqueId);
	}

	public function getFlag(int $flag) : bool{
		if($flag & self::BITFLAG_SECOND_SET){
			return ($this->flags2 & $flag) !== 0;
		}

		return ($this->flags & $flag) !== 0;
	}

	public function setFlag(int $flag, bool $value) : void{
		if($flag & self::BITFLAG_SECOND_SET){
			$flagSet =& $this->flags2;
		}else{
			$flagSet =& $this->flags;
		}

		if($value){
			$flagSet |= $flag;
		}else{
			$flagSet &= ~$flag;
		}
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleAdventureSettings($this);
	}
}
