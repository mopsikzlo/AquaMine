<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol\types;

class MapTrackedObject{
	public const TYPE_ACTOR = 0;
	public const TYPE_BLOCK = 1;

	/** @var int */
	public $type;

	/** @var int Only set if is TYPE_ACTOR */
	public $actorUniqueId;

	/** @var int */
	public $x;
	/** @var int */
	public $y;
	/** @var int */
	public $z;

}
