<?php
declare(strict_types=1);

namespace pocketmine\tile;

use pocketmine\item\Item;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;

trait ContainerTrait{

	protected function initItems(CompoundTag $nbt) : void{
		if(!$nbt->hasTag("Items", ListTag::class)){
			$this->namedtag->setTag("Items", new ListTag([], NBT::TAG_Compound));
		}

		for($i = 0; $i < $this->getSize(); ++$i){
			$this->inventory->setItem($i, $this->getItem($i));
		}
	}

	public function saveNBT(){
		$this->namedtag->setTag("Items", new ListTag([], NBT::TAG_Compound));
		for($index = 0; $index < $this->getSize(); ++$index){
			$this->setItem($index, $this->inventory->getItem($index));
		}
	}

	/**
	 * @param $index
	 *
	 * @return int
	 */
	protected function getSlotIndex(int $index){
		foreach($this->namedtag->getListTag("Items") as $i => $slot){
			/** @var CompoundTag $slot */
			if($slot->getByte("Slot") === $index){
				return (int) $i;
			}
		}

		return -1;
	}

	/**
	 * This method should not be used by plugins, use the Inventory
	 *
	 * @param int $index
	 *
	 * @return Item
	 */
	public function getItem(int $index) : Item{
		$i = $this->getSlotIndex($index);
		if($i < 0){
			return Item::air();
		}else{
			/** @var CompoundTag $itemTag */
			$itemTag = $this->namedtag->getListTag("Items")->get($i);
			return Item::nbtDeserialize($itemTag);
		}
	}

	/**
	 * This method should not be used by plugins, use the Inventory
	 *
	 * @param int  $index
	 * @param Item $item
	 */
	public function setItem(int $index, Item $item){
		$i = $this->getSlotIndex($index);

		$d = $item->nbtSerialize($index);

		$itemsTag = $this->namedtag->getListTag("Items");
		if($item->isNull()){
			if($i >= 0){
				$itemsTag->remove($i);
			}
		}elseif($i < 0){
			$itemsTag->push($d);
		}else{
			$itemsTag->set($i, $d);
		}
	}
}