<?php

declare(strict_types=1);

namespace pocketmine\event\player;

use pocketmine\event\Cancellable;
use pocketmine\math\Vector3;
use pocketmine\Player;

class PlayerPreMoveEvent extends PlayerEvent implements Cancellable{
	public static $handlerList = null;

	/** @var Vector3 */
	private $from;
	/** @var Vector3 */
	private $to;

	/**
	 * @param Player $player
	 * @param Vector3 $from
	 * @param Vector3 $to
	 */
	public function __construct(Player $player, Vector3 $from, Vector3 $to){
		$this->player = $player;
		$this->from = $from;
		$this->to = $to;
	}

	/**
	 * @return Vector3
	 */
	public function getFrom() : Vector3{
		return $this->from;
	}

	/**
	 * @return Vector3
	 */
	public function getTo() : Vector3{
		return $this->to;
	}

	/**
	 * @param Vector3 $to
	 */
	public function setTo(Vector3 $to){
		$this->to = $to;
	}
}