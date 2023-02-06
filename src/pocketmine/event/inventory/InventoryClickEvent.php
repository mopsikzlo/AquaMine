<?php

namespace pocketmine\event\inventory;

use pocketmine\event\Cancellable;
use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\Player;

class InventoryClickEvent extends InventoryEvent implements Cancellable{
	public static $handlerList = null;

	/** @var Player */
	private $who;
	private $slot;
	/** @var Item */
	private $item;

	/**
	 * @param Inventory $inventory
	 * @param Player    $who
	 * @param int       $slot
	 * @param Item      $item
	 */
	public function __construct(Inventory $inventory, Player $who, int $slot, Item $item){
		$this->who = $who;
		$this->slot = $slot;
		$this->item = $item;
		parent::__construct($inventory);
	}

	/**
	 * @return Player
	 */
	public function getWhoClicked(): Player{
		return $this->who;
	}

	/**
	 * @return Player
	 */
	public function getPlayer(): Player{
		return $this->who;
	}

	/**
	 * @return int
	 */
	public function getSlot(): int{
		return $this->slot;
	}

	/**
	 * @return Item
	 */
	public function getItem(): Item{
		return $this->item;
	}
}