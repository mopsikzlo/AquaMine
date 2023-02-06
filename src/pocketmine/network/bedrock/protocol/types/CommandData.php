<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol\types;

class CommandData{
	/** @var string */
	public $commandName;
	/** @var string */
	public $commandDescription;
	/** @var int */
	public $flags;
	/** @var int */
	public $permission;
	/** @var CommandEnum|null */
	public $aliases;
	/** @var CommandParameter[][] */
	public $overloads = [];

}
