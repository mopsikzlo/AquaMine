<?php

declare(strict_types=1);

namespace pocketmine\event\player;

use pocketmine\event\Cancellable;
use pocketmine\Player;

class PlayerToggleSneakEvent extends PlayerEvent implements Cancellable{
	public static $handlerList = null;

	/** @var bool */
	protected $isSneaking;

	/**
	 * @param Player $player
	 * @param bool   $isSneaking
	 */
	public function __construct(Player $player, bool $isSneaking){
		$this->player = $player;
		$this->isSneaking = $isSneaking;
	}

	/**
	 * @return bool
	 */
	public function isSneaking() : bool{
		return $this->isSneaking;
	}

}