<?php

declare(strict_types=1);

namespace pocketmine\inventory;

use pocketmine\Player;
use pocketmine\item\Item;

interface Transaction{

	//Transaction type constants
	public const TYPE_NORMAL = 0;
	public const TYPE_SWAP = 1;
	public const TYPE_HOTBAR = 2; //swap, but with hotbar resend
	public const TYPE_DROP_ITEM = 3;

	/**
	 * @return Inventory
	 */
	public function getInventory();

	/**
	 * @return int
	 */
	public function getSlot() : int;

	/**
	 * @return Item
	 */
	public function getTargetItem() : Item;

	/**
	 * @return float
	 */
	public function getCreationTime() : float;

	/**
	 * @param Player $source
	 * @return bool
	 */
	public function execute(Player $source): bool;

}