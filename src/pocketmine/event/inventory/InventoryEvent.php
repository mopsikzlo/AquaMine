<?php

declare(strict_types=1);

/**
 * Inventory related events
 */
namespace pocketmine\event\inventory;

use pocketmine\entity\Human;
use pocketmine\event\Event;
use pocketmine\inventory\Inventory;

abstract class InventoryEvent extends Event{

	/** @var Inventory */
	protected $inventory;

	public function __construct(Inventory $inventory){
		$this->inventory = $inventory;
	}

	/**
	 * @return Inventory
	 */
	public function getInventory() : Inventory{
		return $this->inventory;
	}

	/**
	 * @return Human[]
	 */
	public function getViewers() : array{
		return $this->inventory->getViewers();
	}
}