<?php

declare(strict_types=1);

namespace pocketmine\event\server;

use pocketmine\event\Cancellable;
use pocketmine\Player;

class RawPacketSendEvent extends ServerEvent implements Cancellable{
	public static $handlerList = null;

	/** @var Player */
	private $player;
	/** @var string */
	private $buffer;

	/**
	 * @param Player $player
	 * @param string $buffer
	 */
	public function __construct(Player $player, string $buffer){
		$this->player = $player;
		$this->buffer = $buffer;
	}

	/**
	 * @return Player
	 */
	public function getPlayer() : Player{
		return $this->player;
	}

	/**
	 * @return string
	 */
	public function getBuffer() : string{
		return $this->buffer;
	}
}