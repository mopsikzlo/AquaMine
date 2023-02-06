<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\adapter\v503\protocol;

class AvailableCommandsPacket extends \pocketmine\network\bedrock\protocol\AvailableCommandsPacket{
	public const ARG_TYPE_COMPARE_OPERATOR = 0x07;
	public const ARG_TYPE_TARGET = 0x08;

	public const ARG_TYPE_WILDCARD_TARGET = 0x0a;

	public const ARG_TYPE_FILEPATH = 0x11;

	public const ARG_TYPE_FULL_INTEGER_RANGE = 0x17;

	public const ARG_TYPE_EQUIPMENT_SLOT = 0x26;
	public const ARG_TYPE_STRING = 0x27;

	public const ARG_TYPE_INT_POSITION = 0x2f;
	public const ARG_TYPE_POSITION = 0x30;

	public const ARG_TYPE_MESSAGE = 0x33;

	public const ARG_TYPE_RAWTEXT = 0x35;

	public const ARG_TYPE_JSON = 0x39;

	public const ARG_TYPE_BLOCK_STATES = 0x43;

	public const ARG_TYPE_COMMAND = 0x46;

}