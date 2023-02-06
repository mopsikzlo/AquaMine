<?php

declare(strict_types=1);

namespace pocketmine\event\block;

use pocketmine\block\Block;
use pocketmine\event\Cancellable;
use pocketmine\item\Item;
use pocketmine\Player;

class BlockBreakEvent extends BlockEvent implements Cancellable{
	public static $handlerList = null;

	/** @var Player */
	protected $player;

	/** @var Item */
	protected $item;

	/** @var bool */
	protected $instaBreak = false;
	protected $blockDrops = [];

	/**
	 * @param Player $player
	 * @param Block  $block
	 * @param Item   $item
	 * @param bool   $instaBreak
	 * @param Item[] $drops
	 */
	public function __construct(Player $player, Block $block, Item $item, bool $instaBreak, array $drops){
		$this->block = $block;
		$this->item = $item;
		$this->player = $player;
		$this->instaBreak = (bool) $instaBreak;
		$this->blockDrops = $drops;
	}

	public function getPlayer(){
		return $this->player;
	}

	public function getItem(){
		return $this->item;
	}

	public function getInstaBreak(){
		return $this->instaBreak;
	}

	/**
	 * @return Item[]
	 */
	public function getDrops(){
		return $this->blockDrops;
	}

	/**
	 * @param Item[] $drops
	 */
	public function setDrops(array $drops){
		$this->blockDrops = $drops;
	}

	/**
	 * @param bool $instaBreak
	 */
	public function setInstaBreak($instaBreak){
		$this->instaBreak = (bool) $instaBreak;
	}
}