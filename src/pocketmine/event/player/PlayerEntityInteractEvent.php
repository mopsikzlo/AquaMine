<?php

declare(strict_types=1);

namespace pocketmine\event\player;

use pocketmine\entity\Entity;
use pocketmine\event\Cancellable;
use pocketmine\item\Item;
use pocketmine\Player;

/**
 * Called when a player interacts an entity.
 */
class PlayerEntityInteractEvent extends PlayerEvent implements Cancellable{
	public static $handlerList = null;

	/** @var Entity */
	protected $entity;
	/** @var Item */
	protected $item;

	public function __construct(Player $player, Entity $entity, Item $item){
		$this->player = $player;
		$this->entity = $entity;
		$this->item = $item;
	}

	/**
	 * @return Entity
	 */
	public function getEntity() : Entity{
		return $this->entity;
	}

	/**
	 * @return Item
	 */
	public function getItem() : Item{
		return $this->item;
	}
}
