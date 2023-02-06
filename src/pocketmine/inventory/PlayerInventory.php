<?php

declare(strict_types=1);

namespace pocketmine\inventory;

use pocketmine\BedrockPlayer;
use pocketmine\entity\Human;
use pocketmine\event\entity\EntityArmorChangeEvent;
use pocketmine\event\entity\EntityInventoryChangeEvent;
use pocketmine\event\entity\EntityOffHandChangeEvent;
use pocketmine\item\Item;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\bedrock\protocol\CreativeContentPacket;
use pocketmine\network\bedrock\protocol\types\inventory\CreativeItem;
use pocketmine\network\mcpe\protocol\ContainerSetContentPacket;
use pocketmine\network\mcpe\protocol\ContainerSetSlotPacket;
use pocketmine\network\mcpe\protocol\MobArmorEquipmentPacket;
use pocketmine\network\mcpe\protocol\MobEquipmentPacket;
use pocketmine\network\mcpe\protocol\types\ContainerIds;
use pocketmine\Player;
use function in_array;
use function is_array;
use function range;

class PlayerInventory extends BaseInventory{

	public const HOTBAR_SIZE = 9;

	public const OFFHAND_INDEX = 40;

	/** @var int */
	protected $itemInHandSlot = 0;

	public function __construct(Human $player, $contents = null){
		parent::__construct($player, InventoryType::get(InventoryType::PLAYER));

		if($contents !== null){
			foreach($contents as $item){
				/** @var CompoundTag $item */
				$slot = $item->getByte("Slot");
				if($slot >= 100 and $slot < 105){ //Armor and offhand
					$this->setItem($this->getSize() + $slot - 100, Item::nbtDeserialize($item), false);
				}else{
					$this->setItem($slot, Item::nbtDeserialize($item), false);
				}
			}
		}
	}

	public function getSize() : int{
		return parent::getSize() - 5; //Remove armor slots and offhand.
	}

	public function setSize(int $size){
		//Do not change
	}

	/**
	 * Returns same index.
	 * 
	 * @deprecated
	 * 
	 * @param int $index
	 *
	 * @return int
	 */
	public function getHotbarSlotIndex($index){
		return $index;
	}

	/**
	 * Links to PlayerInventory::getItem()
	 * 
	 * @deprecated
	 * 
	 * @param int $hotbarSlotIndex
	 *
	 * @return Item
	 */
	public function getHotbarSlotItem(int $hotbarSlotIndex) : Item{
		return $this->getItem($hotbarSlotIndex);
	}

	/**
	 * Returns the slot number the holder is currently holding.
	 * @return int
	 */
	public function getHeldItemSlot(){
		return $this->itemInHandSlot;
	}

	/**
	 * Links to PlayerInventory::getHeldItemSlot
	 * 
	 * @deprecated
	 * 
	 * @return int
	 */
	public function getHeldItemIndex(){
		return $this->itemInHandSlot;
	}

	/**
	 * @param int  $hotbarSlotIndex
	 * @param bool $sendToHolder
	 *
	 * Sets which hotbar slot the player is currently holding.
	 * Allows slot remapping as specified by a MobEquipmentPacket. DO NOT CHANGE SLOT MAPPING IN PLUGINS!
	 * This new implementation is fully compatible with older APIs.
	 */
	public function setHeldItemSlot($hotbarSlotIndex, $sendToHolder = true){
		if(0 <= $hotbarSlotIndex and $hotbarSlotIndex < self::HOTBAR_SIZE){
			$this->itemInHandSlot = $hotbarSlotIndex;
			$item = $this->getItem($hotbarSlotIndex);
			$this->sendHeldItem($this->getHolder()->getViewers());
			if($sendToHolder){
				$this->sendHeldItem($this->getHolder());
			}
		}
	}

	/**
	 * Links to PlayerInventory::setHeldItemSlot
	 * 
	 * @deprecated
	 * 
	 * @param int  $hotbarSlotIndex
	 * @param bool $sendToHolder
	 */
	public function setHeldItemIndex($hotbarSlotIndex, $sendToHolder = true){
		$this->setHeldItemSlot($hotbarSlotIndex);
	}

	/**
	 * Returns the currently-held item.
	 *
	 * @return Item
	 */
	public function getItemInHand(){
		return $this->getItem($this->itemInHandSlot);
	}

	/**
	 * Sets the item in the currently-held slot to the specified item.
	 * @param Item $item
	 *
	 * @return bool
	 */
	public function setItemInHand(Item $item, $send = true){
		return $this->setItem($this->getHeldItemSlot(), $item, $send);
	}

	/**
	 * Sends the currently-held item to specified targets.
	 * @param Player|Player[] $target
	 */
	public function sendHeldItem($target){
		$item = $this->getItemInHand();

		$pk = new MobEquipmentPacket();
		$pk->entityRuntimeId = $this->getHolder()->getId();
		$pk->item = $item;
		$pk->inventorySlot = $pk->hotbarSlot = $this->itemInHandSlot;
		$pk->windowId = ContainerIds::INVENTORY;
		$pk->encode();

		if(!is_array($target)){
			$target->sendDataPacket($pk);
			if($target === $this->getHolder()){
				$this->sendSlot($this->itemInHandSlot, $target);
			}
		}else{
			$this->getHolder()->getLevel()->getServer()->broadcastPacket($target, $pk);
			if(in_array($this->getHolder(), $target, true)){
				$this->sendSlot($this->itemInHandSlot, $this->getHolder());
			}
		}
	}

	/**
	 * @param int  $index
	 * @param Item $before
	 * @param bool $send
	 */
	public function onSlotChange($index, $before, $send){
		if($send){
			$holder = $this->getHolder();
			if(!($holder instanceof Player) or !$holder->spawned){
				return;
			}
			parent::onSlotChange($index, $before, $send);
		}
		if($index === $this->itemInHandSlot){
			$this->sendHeldItem($this->getHolder()->getViewers());
			if($send){
				$this->sendHeldItem($this->getHolder());
			}
		}elseif($index === self::OFFHAND_INDEX){ //Offhand equipment
			$this->sendOffHand($this->getHolder()->getViewers());
			if($send){
				$this->sendOffHand($this->getHolder());
			}
		}elseif($index >= $this->getSize()){ //Armour equipment
			$this->sendArmorSlot($index, $this->getHolder()->getViewers());
			if($send){
				$this->sendArmorSlot($index, $this->getHolder());
			}
		}
	}

	public function getArmorItem($index){
		return $this->getItem($this->getSize() + $index);
	}

	public function setArmorItem($index, Item $item, bool $send = true){
		return $this->setItem($this->getSize() + $index, $item, $send);
	}

	public function getHelmet(){
		return $this->getItem($this->getSize());
	}

	public function getChestplate(){
		return $this->getItem($this->getSize() + 1);
	}

	public function getLeggings(){
		return $this->getItem($this->getSize() + 2);
	}

	public function getBoots(){
		return $this->getItem($this->getSize() + 3);
	}

	public function setHelmet(Item $helmet){
		return $this->setItem($this->getSize(), $helmet);
	}

	public function setChestplate(Item $chestplate){
		return $this->setItem($this->getSize() + 1, $chestplate);
	}

	public function setLeggings(Item $leggings){
		return $this->setItem($this->getSize() + 2, $leggings);
	}

	public function setBoots(Item $boots){
		return $this->setItem($this->getSize() + 3, $boots);
	}

	public function getOffHand(){
		return $this->getItem(self::OFFHAND_INDEX);
	}

	public function setOffHand(Item $item){
		return $this->setItem(self::OFFHAND_INDEX, $item);
	}

	public function setItem(int $index, Item $item, $send = true) : bool{
		if($index < 0 or $index >= $this->size){
			return false;
		}elseif($item->getId() === 0 or $item->getCount() <= 0){
			return $this->clear($index, $send);
		}

		if($index === self::OFFHAND_INDEX){
			$ev = new EntityOffHandChangeEvent($this->getHolder(), $this->getOffHand(), $item);
			$ev->call();
			if($ev->isCancelled() and $this->getHolder() instanceof Human){
				$this->sendOffHand($this->getViewers());
				return false;
			}
			$item = $ev->getNewItem();
		}elseif($index >= $this->getSize()){ //Armor change
			$ev = new EntityArmorChangeEvent($this->getHolder(), $this->getItem($index), $item, $index);
			$ev->call();
			if($ev->isCancelled() and $this->getHolder() instanceof Human){
				$this->sendArmorSlot($index, $this->getViewers());
				return false;
			}
			$item = $ev->getNewItem();
		}else{
			$ev = new EntityInventoryChangeEvent($this->getHolder(), $this->getItem($index), $item, $index);
			$ev->call();
			if($ev->isCancelled()){
				$this->sendSlot($index, $this->getViewers());
				return false;
			}
			$item = $ev->getNewItem();
		}


		$old = $this->getItem($index);
		$this->slots[$index] = clone $item;
		$this->onSlotChange($index, $old, $send);

		return true;
	}

	public function clear(int $index, $send = true) : bool{
		if(isset($this->slots[$index])){
			$item = Item::get(Item::AIR, 0, 0);
			$old = $this->slots[$index];
			if($index === self::OFFHAND_INDEX){
				$ev = new EntityOffHandChangeEvent($this->getHolder(), $old, $item);
				$ev->call();
				if($ev->isCancelled() and $this->getHolder() instanceof Human){
					$this->sendOffHand($this->getViewers());
					return false;
				}
				$item = $ev->getNewItem();
			}elseif($index >= $this->getSize() and $index < $this->size){ //Armor change
				$ev = new EntityArmorChangeEvent($this->getHolder(), $old, $item, $index);
				$ev->call();
				if($ev->isCancelled()){
					if($index >= $this->getSize()){
						$this->sendArmorSlot($index, $this->getViewers());
					}else{
						$this->sendSlot($index, $this->getViewers());
					}
					return false;
				}
				$item = $ev->getNewItem();
			}else{
				$ev = new EntityInventoryChangeEvent($this->getHolder(), $old, $item, $index);
				$ev->call();
				if($ev->isCancelled()){
					if($index >= $this->getSize()){
						$this->sendArmorSlot($index, $this->getViewers());
					}else{
						$this->sendSlot($index, $this->getViewers());
					}
					return false;
				}
				$item = $ev->getNewItem();
			}
			if($item->getId() !== Item::AIR){
				$this->slots[$index] = clone $item;
			}else{
				unset($this->slots[$index]);
			}

			$this->onSlotChange($index, $old, $send);
		}

		return true;
	}

	/**
	 * @return Item[]
	 */
	public function getArmorContents(){
		$armor = [];

		for($i = 0; $i < 4; ++$i){
			$armor[$i] = $this->getItem($this->getSize() + $i);
		}

		return $armor;
	}

	public function clearAll($send = true){
		$limit = $this->getSize() + 5;
		for($index = 0; $index < $limit; ++$index){
			$this->clear($index, $send);
		}
		$this->sendContents($this->getViewers());
	}

	/**
	 * @param Player|Player[] $target
	 */
	public function sendArmorContents($target){
		if($target instanceof Player){
			$target = [$target];
		}

		$armor = $this->getArmorContents();

		$pk = new MobArmorEquipmentPacket();
		$pk->entityRuntimeId = $this->getHolder()->getId();
		$pk->slots = $armor;
		$pk->encode();

		foreach($target as $player){
			if($player === $this->getHolder()){
				$pk2 = new ContainerSetContentPacket();
				$pk2->windowId = ContainerIds::ARMOR;
				$pk2->slots = $armor;
				$pk2->targetEid = $player->getId();
				$player->sendDataPacket($pk2);
			}else{
				$player->sendDataPacket($pk);
			}
		}
	}

	/**
	 * @param Item[] $items
	 */
	public function setArmorContents(array $items){
		for($i = 0; $i < 4; ++$i){
			if(!isset($items[$i]) or !($items[$i] instanceof Item)){
				$items[$i] = Item::get(Item::AIR, 0, 0);
			}

			if($items[$i]->getId() === Item::AIR){
				$this->clear($this->getSize() + $i);
			}else{
				$this->setItem($this->getSize() + $i, $items[$i]);
			}
		}
	}


	/**
	 * @param int             $index
	 * @param Player|Player[] $target
	 */
	public function sendArmorSlot($index, $target){
		if($target instanceof Player){
			$target = [$target];
		}

		$armor = $this->getArmorContents();

		$pk = new MobArmorEquipmentPacket();
		$pk->entityRuntimeId = $this->getHolder()->getId();
		$pk->slots = $armor;
		$pk->encode();

		foreach($target as $player){
			if($player === $this->getHolder()){
				/** @var Player $player */
				$pk2 = new ContainerSetSlotPacket();
				$pk2->windowId = ContainerIds::ARMOR;
				$pk2->slot = $index - $this->getSize();
				$pk2->item = $this->getItem($index);
				$player->sendDataPacket($pk2);
			}else{
				$player->sendDataPacket($pk);
			}
		}
	}

	/**
	 * @param Player|Player[] $target
	 */
	public function sendOffHand($target){
		if($target instanceof Player){
			$target = [$target];
		}

		$item = $this->getOffHand();

		$pk = new MobEquipmentPacket();
		$pk->entityRuntimeId = $this->getHolder()->getId();
		$pk->item = $item;
		$pk->inventorySlot = $pk->hotbarSlot = 0;
		$pk->windowId = ContainerIds::OFFHAND;
		$pk->encode();

		foreach($target as $player){
			if($player === $this->getHolder()){
				/** @var Player $player */
				$pk2 = new ContainerSetContentPacket();
				$pk2->windowId = ContainerIds::OFFHAND;
				$pk2->slots = [$item];
				$pk2->targetEid = $player->getId();
				$player->sendDataPacket($pk2);
			}else{
				$player->sendDataPacket($pk);
			}
		}
	}

	/**
	 * @param Player|Player[] $target
	 */
	public function sendContents($target){
		if($target instanceof Player){
			$target = [$target];
		}

		$pk = new ContainerSetContentPacket();
		$pk->slots = [];

		for($i = 0; $i < $this->getSize(); ++$i){ //Do not send armor by error here
			$pk->slots[$i] = $this->getItem($i);
		}

		//Because PE is stupid and shows 9 less slots than you send it, give it 9 dummy slots so it shows all the REAL slots.
		for($i = $this->getSize(); $i < $this->getSize() + self::HOTBAR_SIZE; ++$i){
			$pk->slots[$i] = Item::get(Item::AIR, 0, 0);
		}

		$pk->hotbar = range(self::HOTBAR_SIZE, self::HOTBAR_SIZE * 2, 1);

		foreach($target as $player){
			if(($id = $player->getWindowId($this)) === -1 or $player->loginProcessed !== true){
				$this->close($player);
				continue;
			}
			$pk->windowId = $id;
			$pk->targetEid = $player->getId(); //TODO: check if this is correct
			$player->sendDataPacket(clone $pk);
			$this->sendHeldItem($player);
		}
	}

	public function sendCreativeContents(){
		$player = $this->getHolder();

		if($player instanceof BedrockPlayer){
			$pk = new CreativeContentPacket();
			if($player instanceof BedrockPlayer and !$player->isSpectator()){
				foreach(Item::getCreativeItems() as $i => $item){
					$pk->items[$i] = new CreativeItem($i, $item);
				}
			}
			$player->sendDataPacket($pk);
		}else{
			$pk = new ContainerSetContentPacket();
			$pk->windowId = ContainerIds::CREATIVE;
			if($player->getGamemode() === Player::CREATIVE){
				foreach(Item::getCreativeItems() as $i => $item){
					$pk->slots[$i] = clone $item;
				}
			}
			$pk->targetEid = $this->getHolder()->getId();
			$player->sendDataPacket($pk);
		}
	}

	/**
	 * @param int             $index
	 * @param Player|Player[] $target
	 */
	public function sendSlot($index, $target){
		if($index === self::OFFHAND_INDEX){
			$this->sendOffHand($target);
		}elseif($index >= $this->getSize()){ //Armor
			$this->sendArmorSlot($index, $target);
		}else{
			if($target instanceof Player){
				$target = [$target];
			}

			$pk = new ContainerSetSlotPacket();
			$pk->slot = $index;
			$pk->item = clone $this->getItem($index);

			foreach($target as $player){
				if($player === $this->getHolder()){
					/** @var Player $player */
					$pk->windowId = 0;
					$player->sendDataPacket(clone $pk);
				}else{
					if(($id = $player->getWindowId($this)) === -1){
						$this->close($player);
						continue;
					}
					$pk->windowId = $id;
					$player->sendDataPacket(clone $pk);
				}
			}
		}
	}

	/**
	 * @return Human|Player
	 */
	public function getHolder(){
		return parent::getHolder();
	}
}
