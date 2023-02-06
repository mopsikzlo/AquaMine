<?php

declare(strict_types=1);

namespace pocketmine\event\player;

use pocketmine\event\Cancellable;
use pocketmine\item\Item;
use pocketmine\Player;

class PlayerItemHeldEvent extends PlayerEvent implements Cancellable{
	public static $handlerList = null;

	/** @var Item */
	private $item;
	/** @var int */
	private $hotbarSlot;
	/** @var int */
	private $inventorySlot;

	public function __construct(Player $player, Item $item, int $inventorySlot, int $hotbarSlot){
		$this->player = $player;
		$this->item = $item;
		$this->inventorySlot = $inventorySlot;
		$this->hotbarSlot = $hotbarSlot;
	}

	/**
	 * Returns the hotbar slot the player is attempting to hold.
	 * @return int
	 */
	public function getSlot() : int{
		return $this->hotbarSlot;
	}

	/**
	 * @return int
	 */
	public function getInventorySlot() : int{
		return $this->inventorySlot;
	}

	public function getItem() : Item{
		return $this->item;
	}

}