<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol\types\inventory;

use pocketmine\item\Item;

class CreativeItem{

	/** @var int */
	public $creativeItemNetworkId;
	/** @var Item */
	public $item;

	public function __construct(int $creativeItemNetworkId = -1, ?Item $item = null){
		$this->creativeItemNetworkId = $creativeItemNetworkId;
		$this->item = $item;
	}
}