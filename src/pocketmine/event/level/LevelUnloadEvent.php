<?php

declare(strict_types=1);

namespace pocketmine\event\level;

use pocketmine\event\Cancellable;

/**
 * Called when a Level is unloaded
 */
class LevelUnloadEvent extends LevelEvent implements Cancellable{
	public static $handlerList = null;
}