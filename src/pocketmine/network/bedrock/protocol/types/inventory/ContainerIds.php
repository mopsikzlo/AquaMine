<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol\types\inventory;

interface ContainerIds{

	public const NONE = -1;
	public const INVENTORY = 0;
	public const FIRST = 1;
	public const LAST = 100;
	public const OFFHAND = 119;
	public const ARMOR = 120;
	public const HOTBAR = 122;
	public const FIXED_INVENTORY = 123;
	public const UI = 124;

}
