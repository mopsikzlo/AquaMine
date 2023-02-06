<?php

declare(strict_types=1);

namespace pocketmine\event\block;

use pocketmine\event\Cancellable;

/**
 * Called when a block tries to be updated due to a neighbor change
 */
class BlockUpdateEvent extends BlockEvent implements Cancellable{
	public static $handlerList = null;

}