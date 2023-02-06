<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol\types\actor;

interface ActorMetadataTypes{

	public const BYTE = 0;
	public const SHORT = 1;
	public const INT = 2;
	public const FLOAT = 3;
	public const STRING = 4;
	public const NBT = 5;
	public const POS = 6;
	public const LONG = 7;
	public const VECTOR3F = 8;
}
