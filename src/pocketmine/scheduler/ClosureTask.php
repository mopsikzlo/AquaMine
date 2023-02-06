<?php

declare(strict_types=1);

namespace pocketmine\scheduler;

use pocketmine\utils\Utils;

/**
 * Task implementation which allows closures to be called by a scheduler.
 *
 * Example usage:
 *
 * ```
 * Server::getInstance()->scheduleRepeatingTask(new ClosureTask(function(int $currentTick) : void{
 *     echo "HI on $currentTick\n";
 * }), 1);
 * ```
 */
class ClosureTask extends Task{

	/** @var \Closure */
	private $closure;

	/**
	 * @param \Closure $closure Must accept only ONE parameter, $currentTick
	 */
	public function __construct(\Closure $closure){
		Utils::validateCallableSignature(function(int $currentTick) : void{}, $closure);
		$this->closure = $closure;
	}

	public function getName() : string{
		return Utils::getNiceClosureName($this->closure);
	}

	public function onRun(int $currentTick){
		($this->closure)($currentTick);
	}
}