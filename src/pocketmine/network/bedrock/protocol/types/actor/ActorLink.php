<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol\types\actor;

class ActorLink{

	public const TYPE_REMOVE = 0;
	public const TYPE_RIDER = 1;
	public const TYPE_PASSENGER = 2;

	/** @var int */
	public $fromActorUniqueId;
	/** @var int */
	public $toActorUniqueId;
	/** @var int */
	public $type;
	/** @var bool */
	public $immediate; //for dismounting on mount death
	/** @var bool */
	public $riderInitiated;

	public function __construct(?int $fromActorUniqueId = null, ?int $toActorUniqueId = null, ?int $type = null, bool $immediate = false, bool $riderInitiated = false){
		$this->fromActorUniqueId = $fromActorUniqueId;
		$this->toActorUniqueId = $toActorUniqueId;
		$this->type = $type;
		$this->immediate = $immediate;
		$this->riderInitiated = $riderInitiated;
	}
}
