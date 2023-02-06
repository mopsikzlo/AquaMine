<?php

declare(strict_types=1);

namespace pocketmine\inventory;

use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\network\mcpe\protocol\BlockEventPacket;
use pocketmine\Player;
use pocketmine\tile\Chest;

use function array_merge;
use function array_slice;
use function count;

class DoubleChestInventory extends ChestInventory implements InventoryHolder{
	/** @var ChestInventory */
	private $left;
	/** @var ChestInventory */
	private $right;

	public function __construct(Chest $left, Chest $right){
		$this->left = $left->getRealInventory();
		$this->right = $right->getRealInventory();
		$items = array_merge($this->left->getContents(), $this->right->getContents());
		BaseInventory::__construct($this, InventoryType::get(InventoryType::DOUBLE_CHEST), $items);
	}

	public function getInventory(){
		return $this;
	}

	public function getHolder(){
		return $this->left->getHolder();
	}

	public function getItem(int $index) : Item{
		return $index < $this->left->getSize() ? $this->left->getItem($index) : $this->right->getItem($index - $this->right->getSize());
	}

	public function setItem(int $index, Item $item, $send = true) : bool{
		return $index < $this->left->getSize() ? $this->left->setItem($index, $item, $send) : $this->right->setItem($index - $this->right->getSize(), $item, $send);
	}

	public function clear(int $index, $send = true) : bool{
		return $index < $this->left->getSize() ? $this->left->clear($index, $send) : $this->right->clear($index - $this->right->getSize(), $send);
	}

	public function getContents() : array{
		$contents = [];
		for($i = 0; $i < $this->getSize(); ++$i){
			$contents[$i] = $this->getItem($i);
		}

		return $contents;
	}

	/**
	 * @param Item[] $items
	 */
	public function setContents(array $items, $send = true){
		if(count($items) > $this->size){
			$items = array_slice($items, 0, $this->size, true);
		}


		for($i = 0; $i < $this->size; ++$i){
			if(!isset($items[$i])){
				if($i < $this->left->size){
					if(isset($this->left->slots[$i])){
						$this->clear($i);
					}
				}elseif(isset($this->right->slots[$i - $this->left->size])){
					$this->clear($i);
				}
			}elseif(!$this->setItem($i, $items[$i])){
				$this->clear($i);
			}
		}
		if($send)
			$this->sendContents($this->getViewers());
	}

	public function onOpen(Player $who){
		parent::onOpen($who);

		if(count($this->getViewers()) === 1){
			$pk = new BlockEventPacket();
			$pk->x = $this->right->getHolder()->getX();
			$pk->y = $this->right->getHolder()->getY();
			$pk->z = $this->right->getHolder()->getZ();
			$pk->eventType = BlockEventPacket::TYPE_CHEST;
			$pk->eventData = BlockEventPacket::DATA_CHEST_OPEN;
			if(($level = $this->right->getHolder()->getLevel()) instanceof Level){
				$level->addChunkPacket($this->right->getHolder()->getX() >> 4, $this->right->getHolder()->getZ() >> 4, $pk);
			}
		}
	}

	public function onClose(Player $who){
		if(count($this->getViewers()) === 1){
			$pk = new BlockEventPacket();
			$pk->x = $this->right->getHolder()->getX();
			$pk->y = $this->right->getHolder()->getY();
			$pk->z = $this->right->getHolder()->getZ();
			$pk->eventType = BlockEventPacket::TYPE_CHEST;
			$pk->eventData = BlockEventPacket::DATA_CHEST_CLOSED;
			if(($level = $this->right->getHolder()->getLevel()) instanceof Level){
				$level->addChunkPacket($this->right->getHolder()->getX() >> 4, $this->right->getHolder()->getZ() >> 4, $pk);
			}
		}
		parent::onClose($who);
	}

	/**
	 * @return ChestInventory
	 */
	public function getLeftSide() : ChestInventory{
		return $this->left;
	}

	/**
	 * @return ChestInventory
	 */
	public function getRightSide() : ChestInventory{
		return $this->right;
	}
}
