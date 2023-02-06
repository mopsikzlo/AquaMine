<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\adapter\v486\protocol;

class AvailableCommandsPacket extends \pocketmine\network\bedrock\protocol\AvailableCommandsPacket{
	public const ARG_TYPE_WILDCARD_TARGET = 0x08;

	public const ARG_TYPE_STRING = 0x20;

	public const ARG_TYPE_BLOCK_POSITION = 0x28;
	public const ARG_TYPE_POSITION       = 0x29;

	public const ARG_TYPE_MESSAGE = 0x2c;

	public const ARG_TYPE_RAWTEXT = 0x2e;

	public const ARG_TYPE_JSON = 0x32;

	public const ARG_TYPE_COMMAND = 0x3f;

}