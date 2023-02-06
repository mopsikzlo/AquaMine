<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\adapter\v407\protocol;

#include <rules/DataPacket.h>


class PlayerActionPacket extends \pocketmine\network\bedrock\adapter\v422\protocol\PlayerActionPacket{
	use PacketTrait;

	public const ACTION_DIMENSION_CHANGE_REQUEST = 13; //sent when dying in different dimension

}