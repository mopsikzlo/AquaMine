<?php

declare(strict_types=1);

namespace pocketmine\event\player;

use pocketmine\event\Cancellable;
use pocketmine\item\Item;
use pocketmine\Player;

/**
 * Called when a player eats something
 */
class PlayerItemConsumeEvent extends PlayerEvent implements Cancellable{
	public static $handlerList = null;

	/** @var Item */
	private $item;

	/**
	 * @param Player $player
	 * @param Item   $item
	 */
	public function __construct(Player $player, Item $item){
		$this->player = $player;
		$this->item = $item;
	}

	/**
	 * @return Item
	 */
	public function getItem() : Item{
		return clone $this->item;
	}

}