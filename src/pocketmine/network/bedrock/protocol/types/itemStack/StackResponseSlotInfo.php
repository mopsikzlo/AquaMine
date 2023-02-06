<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol\types\itemStack;

class StackResponseSlotInfo{

	/** @var int */
	public $slot;
	/** @var int */
	public $hotbarSlot;
	/** @var int */
	public $count;
	/** @var int */
	public $stackNetworkId;
	/** @var string */
	public $customName;
	/** @var int */
	public $durabilityCorrection;

	public function __construct(int $slot = -1, int $hotbarSlot = -1, int $count = -1, int $stackNetworkId = -1, string $customName = "", int $durabilityCorrection = 0){
		$this->slot = $slot;
		$this->hotbarSlot = $hotbarSlot;
		$this->count = $count;
		$this->stackNetworkId = $stackNetworkId;
		$this->customName = $customName;
		$this->durabilityCorrection = $durabilityCorrection;
	}
}