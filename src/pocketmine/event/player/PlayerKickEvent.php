<?php

declare(strict_types=1);

namespace pocketmine\event\player;

use pocketmine\event\Cancellable;
use pocketmine\event\TextContainer;
use pocketmine\Player;

/**
 * Called when a player leaves the server
 */
class PlayerKickEvent extends PlayerEvent implements Cancellable{
	public static $handlerList = null;

	/** @var TextContainer|string */
	protected $quitMessage;

	/** @var string */
	protected $reason;

	/**
	 * PlayerKickEvent constructor.
	 *
	 * @param Player               $player
	 * @param string               $reason
	 * @param TextContainer|string $quitMessage
	 */
	public function __construct(Player $player, string $reason, $quitMessage){
		$this->player = $player;
		$this->quitMessage = $quitMessage;
		$this->reason = $reason;
	}

	public function getReason() : string{
		return $this->reason;
	}

	/**
	 * @param TextContainer|string $quitMessage
	 */
	public function setQuitMessage($quitMessage){
		$this->quitMessage = $quitMessage;
	}

	/**
	 * @return TextContainer|string
	 */
	public function getQuitMessage(){
		return $this->quitMessage;
	}

}