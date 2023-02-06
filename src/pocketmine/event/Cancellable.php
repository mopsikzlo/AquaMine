<?php

declare(strict_types=1);

namespace pocketmine\event;


/**
 * Events that can be cancelled must use the interface Cancellable
 */
interface Cancellable{
	/**
	 * @return bool
	 */
	public function isCancelled() : bool;

	/**
	 * @param bool $value
	 */
	public function setCancelled(bool $value = true);
}
