<?php

declare(strict_types=1);

namespace pocketmine\event\player;

use pocketmine\event\Cancellable;
use pocketmine\Player;

class PlayerToggleFlightEvent extends PlayerEvent implements Cancellable{
	public static $handlerList = null;

	/** @var bool */
	protected $isFlying;

	/**
	 * @param Player $player
	 * @param bool   $isFlying
	 */
	public function __construct(Player $player, bool $isFlying){
		$this->player = $player;
		$this->isFlying = $isFlying;
	}

	/**
	 * @return bool
	 */
	public function isFlying() : bool{
		return $this->isFlying;
	}

}