<?php

declare(strict_types=1);

namespace pocketmine\event\player;

use pocketmine\event\Cancellable;
use pocketmine\Player;

/**
 * Called when a player has its gamemode changed
 */
class PlayerGameModeChangeEvent extends PlayerEvent implements Cancellable{
	public static $handlerList = null;

	/** @var int */
	protected $gamemode;

	public function __construct(Player $player, int $newGamemode){
		$this->player = $player;
		$this->gamemode = $newGamemode;
	}

	public function getNewGamemode() : int{
		return $this->gamemode;
	}

}