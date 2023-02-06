<?php

declare(strict_types=1);

namespace pocketmine\event\entity;

use pocketmine\entity\Entity;
use pocketmine\event\Cancellable;
use pocketmine\item\Item;

class EntityOffHandChangeEvent extends EntityEvent implements Cancellable{
	public static $handlerList = null;

	private $oldItem;
	private $newItem;

	public function __construct(Entity $entity, Item $oldItem, Item $newItem){
		$this->entity = $entity;
		$this->oldItem = $oldItem;
		$this->newItem = $newItem;
	}

	public function getNewItem(){
		return $this->newItem;
	}

	public function setNewItem(Item $item){
		$this->newItem = $item;
	}

	public function getOldItem(){
		return $this->oldItem;
	}
}