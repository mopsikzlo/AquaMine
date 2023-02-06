<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\adapter\v422\protocol;

#include <rules/DataPacket.h>


class PlayerActionPacket extends \pocketmine\network\bedrock\adapter\v503\protocol\PlayerActionPacket{

	public const ACTION_CONTINUE_BREAK = 18;
}