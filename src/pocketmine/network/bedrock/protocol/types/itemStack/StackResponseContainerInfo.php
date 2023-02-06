<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol\types\itemStack;

class StackResponseContainerInfo{

	/** @var int */
	public $containerId;
	/** @var StackResponseSlotInfo[] */
	public $slotInfo = [];

	public function __construct(int $containerId = -1, array $slotInfo = []){
		$this->containerId = $containerId;
		$this->slotInfo = $slotInfo;
	}
}