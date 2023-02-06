<?php

declare(strict_types=1);

namespace pocketmine\event\player;

use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\event\TextContainer;
use pocketmine\item\Item;
use pocketmine\Player;

class PlayerDeathEvent extends EntityDeathEvent{
	public static $handlerList = null;

	/** @var TextContainer|string */
	private $deathMessage;
	private $keepInventory = false;

	/**
	 * @param Player               $entity
	 * @param Item[]               $drops
	 * @param string|TextContainer $deathMessage
	 */
	public function __construct(Player $entity, array $drops, $deathMessage){
		parent::__construct($entity, $drops);
		$this->deathMessage = $deathMessage;
	}

	/**
	 * @return Player
	 */
	public function getEntity() : Player{
		return $this->entity;
	}

	/**
	 * @return Player
	 */
	public function getPlayer() : Player{
		return $this->entity;
	}

	/**
	 * @return TextContainer|string
	 */
	public function getDeathMessage(){
		return $this->deathMessage;
	}

	/**
	 * @param TextContainer|string $deathMessage
	 */
	public function setDeathMessage($deathMessage){
		$this->deathMessage = $deathMessage;
	}

	public function getKeepInventory() : bool{
		return $this->keepInventory;
	}

	public function setKeepInventory(bool $keepInventory){
		$this->keepInventory = $keepInventory;
	}

}