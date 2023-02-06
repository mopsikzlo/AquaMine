<?php

declare(strict_types=1);

namespace pocketmine\event\level;

/**
 * Called when a Level is loaded
 */
class LevelLoadEvent extends LevelEvent{
	public static $handlerList = null;
}