<?php

declare(strict_types=1);

namespace pocketmine\event\player;

use pocketmine\block\Block;
use pocketmine\event\Cancellable;
use pocketmine\Player;

class PlayerBedEnterEvent extends PlayerEvent implements Cancellable{
	public static $handlerList = null;

	/** @var Block */
	private $bed;

	public function __construct(Player $player, Block $bed){
		$this->player = $player;
		$this->bed = $bed;
	}

	/**
	 * @return Block
	 */
	public function getBed() : Block{
		return $this->bed;
	}

}