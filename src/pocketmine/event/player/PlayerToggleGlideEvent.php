<?php

declare(strict_types=1);

namespace pocketmine\event\player;

use pocketmine\event\Cancellable;
use pocketmine\Player;

class PlayerToggleGlideEvent extends PlayerEvent implements Cancellable{
	public static $handlerList = null;

	/** @var bool */
	protected $isGliding;

	/**
	 * @param Player $player
	 * @param bool   $isGliding
	 */
	public function __construct(Player $player, bool $isGliding){
		$this->player = $player;
		$this->isGliding = $isGliding;
	}

	/**
	 * @return bool
	 */
	public function isGliding() : bool{
		return $this->isGliding;
	}

}