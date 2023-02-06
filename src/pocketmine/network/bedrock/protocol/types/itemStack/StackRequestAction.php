<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol\types\itemStack;

use pocketmine\network\bedrock\protocol\DataPacket;

abstract class StackRequestAction{

	/**
	 * @return int
	 */
	abstract public function getActionId() : int;

	/**
	 * @param DataPacket $stream
	 *
	 * @throws \OutOfBoundsException
	 * @throws \UnexpectedValueException
	 */
	abstract public function decode(DataPacket $stream) : void;

	abstract public function encode(DataPacket $stream) : void;
}