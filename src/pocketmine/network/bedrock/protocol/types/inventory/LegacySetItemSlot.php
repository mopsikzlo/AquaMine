<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol\types\inventory;

class LegacySetItemSlot{

	/** @var int */
	public $containerId;
	/** @var int[] */
	public $slots = [];

	public function __construct(int $containerId = -1, array $slots = []){
		$this->containerId = $containerId;
		$this->slots = $slots;
	}
}