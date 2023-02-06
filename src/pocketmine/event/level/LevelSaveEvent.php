<?php

declare(strict_types=1);

namespace pocketmine\event\level;

/**
 * Called when a Level is saved
 */
class LevelSaveEvent extends LevelEvent{
	public static $handlerList = null;
}