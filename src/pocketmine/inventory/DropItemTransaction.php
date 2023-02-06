<?php

namespace pocketmine\inventory;

use pocketmine\item\Item;
use pocketmine\Player;

class DropItemTransaction extends BaseTransaction{

	public const TRANSACTION_TYPE = Transaction::TYPE_DROP_ITEM;

	protected $inventory = null;

	protected $slot = null;

	protected $sourceItem = null;

	/**
	 * @param Item $droppedItem
	 */
	public function __construct(Item $droppedItem){
		$this->targetItem = $droppedItem;
	}

	public function setSourceItem(Item $item){
		//Nothing to update
	}

	public function getInventory(){
		return null;
	}

	public function getSlot(): int{
		return -1;
	}

	public function sendSlotUpdate(Player $source){
		foreach($source->getWindows() as $inv){
			$inv->sendContents($source);
		}
	}

	public function getChange(){
		return ["in" => $this->getTargetItem(),
				"out" => null];
	}

	public function execute(Player $source): bool{
		$droppedItem = $this->getTargetItem();
		if(!$source->getServer()->allowInventoryCheats and !$source->isCreative()){
			if(!$source->getFloatingInventory()->contains($droppedItem)){
				return false;
			}
			$source->getFloatingInventory()->removeItem($droppedItem);
		}
		$source->dropItem($droppedItem);
		return true;
	}
}
