<?php

declare(strict_types=1);

namespace pocketmine\event\player;

use pocketmine\event\Cancellable;
use pocketmine\Player;

class PlayerToggleSprintEvent extends PlayerEvent implements Cancellable{
	public static $handlerList = null;

	/** @var bool */
	protected $isSprinting;

	/**
	 * @param Player $player
	 * @param bool   $isSprinting
	 */
	public function __construct(Player $player, bool $isSprinting){
		$this->player = $player;
		$this->isSprinting = $isSprinting;
	}

	/**
	 * @return bool
	 */
	public function isSprinting() : bool{
		return $this->isSprinting;
	}

}