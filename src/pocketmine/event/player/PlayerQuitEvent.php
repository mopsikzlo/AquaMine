<?php

declare(strict_types=1);

namespace pocketmine\event\player;

use pocketmine\event\TranslationContainer;
use pocketmine\Player;

/**
 * Called when a player leaves the server
 */
class PlayerQuitEvent extends PlayerEvent{
	public static $handlerList = null;

	/** @var TranslationContainer|string */
	protected $quitMessage;
	/** @var string */
	protected $reason;

	/**
	 * @param Player                      $player
	 * @param TranslationContainer|string $quitMessage
	 * @param string                      $reason
	 */
	public function __construct(Player $player, $quitMessage, string $reason){
		$this->player = $player;
		$this->quitMessage = $quitMessage;
		$this->reason = $reason;
	}

	/**
	 * @param TranslationContainer|string $quitMessage
	 */
	public function setQuitMessage($quitMessage){
		$this->quitMessage = $quitMessage;
	}

	/**
	 * @return TranslationContainer|string
	 */
	public function getQuitMessage(){
		return $this->quitMessage;
	}

	/**
	 * @return string
	 */
	public function getReason() : string{
		return $this->reason;
	}
}
