<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol\types\itemStack;

class StackRequestSlotInfo{

	/** @var int */
	public $containerId;
	/** @var int */
	public $slot;
	/** @var int */
	public $stackNetworkId;

	public function __construct(int $containerId = -1, int $slot = -1, int $stackNetworkId = -1){
		$this->containerId = $containerId;
		$this->slot = $slot;
		$this->stackNetworkId = $stackNetworkId;
	}
}