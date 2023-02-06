<?php

declare(strict_types=1);

namespace pocketmine\event\inventory;

use pocketmine\event\block\BlockEvent;
use pocketmine\event\Cancellable;
use pocketmine\item\Item;
use pocketmine\tile\Furnace;

class FurnaceSmeltEvent extends BlockEvent implements Cancellable{
	public static $handlerList = null;

	/** @var Furnace */
	private $furnace;
	/** @var Item */
	private $source;
	/** @var Item */
	private $result;

	/**
	 * @param Furnace $furnace
	 * @param Item $source
	 * @param Item $result
	 */
	public function __construct(Furnace $furnace, Item $source, Item $result){
		parent::__construct($furnace->getBlock());
		$this->source = clone $source;
		$this->source->setCount(1);
		$this->result = $result;
		$this->furnace = $furnace;
	}

	/**
	 * @return Furnace
	 */
	public function getFurnace() : Furnace{
		return $this->furnace;
	}

	/**
	 * @return Item
	 */
	public function getSource() : Item{
		return $this->source;
	}

	/**
	 * @return Item
	 */
	public function getResult() : Item{
		return $this->result;
	}

	/**
	 * @param Item $result
	 */
	public function setResult(Item $result){
		$this->result = $result;
	}
}