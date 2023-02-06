<?php

declare(strict_types=1);

namespace pocketmine\event\player\fish;

use pocketmine\entity\FishingHook;
use pocketmine\event\Cancellable;
use pocketmine\event\player\PlayerEvent;
use pocketmine\Player;

class FishingRodCaughtFishEvent extends PlayerEvent implements Cancellable{
	public static $handlerList = null;

	/** @var FishingHook */
	protected $hook;

	public function __construct(Player $fisher, FishingHook $hook){
		$this->player = $fisher;
		$this->hook = $hook;
	}
	/**
	 * @return FishingHook
	 */
	public function getHook() : FishingHook{
		return $this->hook;
	}
}