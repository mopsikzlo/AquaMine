<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol\types;

interface PlayerPermissions{

	public const CUSTOM = 3;
	public const OPERATOR = 2;
	public const MEMBER = 1;
	public const VISITOR = 0;

}
