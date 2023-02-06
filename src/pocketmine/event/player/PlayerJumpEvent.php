<?php

declare(strict_types=1);

namespace pocketmine\event\player;

use pocketmine\Player;

/**
 * Called when a player jumps
 */
class PlayerJumpEvent extends PlayerEvent{
	public static $handlerList = null;

	/**
	 * PlayerJumpEvent constructor.
	 *
	 * @param Player $player
	 */
	public function __construct(Player $player){
		$this->player = $player;
	}

}
