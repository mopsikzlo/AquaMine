<?php

declare(strict_types=1);

namespace pocketmine\event\server;

use pocketmine\event\Cancellable;
use pocketmine\network\mcpe\protocol\DataPacket;
use pocketmine\Player;

class DataPacketSendEvent extends ServerEvent implements Cancellable{
	public static $handlerList = null;

	/** @var DataPacket */
	private $packet;
	/** @var Player */
	private $player;

	/**
	 * @param Player $player
	 * @param DataPacket $packet
	 */
	public function __construct(Player $player, DataPacket $packet){
		$this->packet = $packet;
		$this->player = $player;
	}

	/**
	 * @return DataPacket
	 */
	public function getPacket() : DataPacket{
		return $this->packet;
	}

	/**
	 * @return Player
	 */
	public function getPlayer() : Player{
		return $this->player;
	}
}