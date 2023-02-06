<?php

declare(strict_types=1);


namespace pocketmine\event\player\cheat;

use pocketmine\event\Cancellable;
use pocketmine\Player;
use pocketmine\math\Vector3;

/**
 * Called when a player attempts to perform movement cheats such as clipping through blocks.
 */
class PlayerIllegalMoveEvent extends PlayerCheatEvent implements Cancellable{
	public static $handlerList = null;

	/** @var Vector3 */
	private $attemptedPosition;

	/**
	 * @param Player  $player
	 * @param Vector3 $attemptedPosition
	 */
	public function __construct(Player $player, Vector3 $attemptedPosition){
		$this->attemptedPosition = $attemptedPosition;
		$this->player = $player;
	}

	/**
	 * Returns the position the player attempted to move to.
	 * @return Vector3
	 */
	public function getAttemptedPosition() : Vector3{
		return $this->attemptedPosition;
	}

}