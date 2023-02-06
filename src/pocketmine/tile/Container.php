<?php

declare(strict_types=1);

namespace pocketmine\tile;

use pocketmine\item\Item;

interface Container{

	/**
	 * @param int $index
	 *
	 * @return Item
	 */
	public function getItem(int $index) : Item;

	/**
	 * @param int  $index
	 * @param Item $item
	 */
	public function setItem(int $index, Item $item);

	/**
	 * @return int
	 */
	public function getSize() : int;
}
