<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol\types\inventory;

use pocketmine\item\Item;

class ItemInstance{

	/** @var int */
	public $stackNetworkId;
	/** @var Item */
	public $stack;

	public function __construct(?int $stackNetworkId = null, ?Item $stack = null){
		$this->stackNetworkId = $stackNetworkId;
		$this->stack = $stack;
	}

	public static function legacy(Item $itemStack) : self{
		return new self($itemStack->isNull() ? 0 : 1, $itemStack);
	}
}