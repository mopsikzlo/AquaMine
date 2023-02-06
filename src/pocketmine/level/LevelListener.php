<?php

declare(strict_types=1);

namespace pocketmine\level;

/**
 * This interface allows you to listen for events related to levels. This is only used for handling level unloading for now.
 *
 * @see Level::registerLevelListener()
 * @see Level::unregisterLevelListener()
 */
interface LevelListener{

	/**
	 * This method will be called when a Level is unloaded.
	 * 
	 * @param Level $level
	 */
	public function onLevelUnloaded(Level $level);
}