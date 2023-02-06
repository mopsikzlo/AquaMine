<?php

declare(strict_types=1);

namespace pocketmine\event\player\fish;

use pocketmine\entity\Entity;
use pocketmine\entity\FishingHook;
use pocketmine\event\Cancellable;
use pocketmine\event\player\PlayerEvent;
use pocketmine\Player;

class FishingRodCaughtEntityEvent extends PlayerEvent implements Cancellable{
	public static $handlerList = null;

	/** @var FishingHook */
	protected $hook;
	/** @var Entity */
	protected $hookedEntity;
	/** @var float */
	protected $force;

	public function __construct(Player $fisher, FishingHook $hook, Entity $hookedEntity, float $force){
		$this->player = $fisher;
		$this->hook = $hook;
		$this->hookedEntity = $hookedEntity;
		$this->force = $force;
	}

	/**
	 * @return Entity
	 */
	public function getHookedEntity() : Entity{
		return $this->hookedEntity;
	}

	/**
	 * @return FishingHook
	 */
	public function getHook() : FishingHook{
		return $this->hook;
	}

	/**
	 * @return float
	 */
	public function getForce() : float{
		return $this->force;
	}

	/**
	 * @param float $force
	 */
	public function setForce(float $force) : void{
		$this->force = $force;
	}
}