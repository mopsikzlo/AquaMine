<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\adapter\v422\protocol;

#include <rules/DataPacket.h>


class AdventureSettingsPacket extends \pocketmine\network\bedrock\protocol\AdventureSettingsPacket{

	public const BUILD_AND_MINE = 0x01 | self::BITFLAG_SECOND_SET;

}