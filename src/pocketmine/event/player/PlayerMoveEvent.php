<?php

declare(strict_types=1);

namespace pocketmine\event\player;

use pocketmine\event\Cancellable;
use pocketmine\level\Location;
use pocketmine\Player;

class PlayerMoveEvent extends PlayerEvent implements Cancellable{
	public static $handlerList = null;

	/** @var Location */
	private $from;
	/** @var Location */
	private $to;

	/**
	 * @param Player $player
	 * @param Location $from
	 * @param Location $to
	 */
	public function __construct(Player $player, Location $from, Location $to){
		$this->player = $player;
		$this->from = $from;
		$this->to = $to;
	}

	/**
	 * @return Location
	 */
	public function getFrom() : Location{
		return $this->from;
	}

	/**
	 * @param Location $from
	 */
	public function setFrom(Location $from){
		$this->from = $from;
	}

	/**
	 * @return Location
	 */
	public function getTo() : Location{
		return $this->to;
	}

	/**
	 * @param Location $to
	 */
	public function setTo(Location $to){
		$this->to = $to;
	}
}