<?php

declare(strict_types=1);

namespace pocketmine\inventory;

use pocketmine\item\Item;
use pocketmine\utils\UUID;

interface Recipe{

	/**
	 * @return Item
	 */
	public function getResult() : Item;

	public function registerToCraftingManager();

	/**
	 * @return UUID|null
	 */
	public function getId();

	/**
	 * @param UUID $id
	 */
	public function setId(UUID $id);
}