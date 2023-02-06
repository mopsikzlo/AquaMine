<?php

declare(strict_types=1);

namespace pocketmine\event\explosion;

use pocketmine\event\Cancellable;

class ExplosionExplodeBEvent extends ExplosionEvent implements Cancellable{
	public static $handlerList = null;
}