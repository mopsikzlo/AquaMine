<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\adapter\v428\protocol\types;

use Exception;
use InvalidArgumentException;
use pocketmine\network\bedrock\protocol\DataPacket;
use pocketmine\network\bedrock\protocol\InventoryTransactionPacket;
use pocketmine\network\bedrock\adapter\v428\protocol\InventoryTransactionPacket as InventoryTransactionPacket428;
use pocketmine\network\bedrock\protocol\types\inventory\ItemInstance;
use \pocketmine\network\bedrock\protocol\types\NetworkInventoryAction as BedrockNetworkInventoryAction;

use pocketmine\network\mcpe\NetworkBinaryStream;
use function count;

class NetworkInventoryAction extends BedrockNetworkInventoryAction{
	/** @var int */
	public $stackNetworkId = 0;

	/**
	 * @param NetworkBinaryStream $packet
	 *
	 * @return $this
	 * @throws \UnexpectedValueException
	 * @throws \OutOfBoundsException
	 */
	public function read(NetworkBinaryStream $packet) : BedrockNetworkInventoryAction{
		if(!$packet instanceof InventoryTransactionPacket428){
			throw new InvalidArgumentException("Expected \$packet to be InventoryTransactionPacket (v428)");
		}

		$this->sourceType = $packet->getUnsignedVarInt();

		switch ($this->sourceType){
			case self::SOURCE_CONTAINER:
				$this->windowId = $packet->getVarInt();
				break;
			case self::SOURCE_WORLD:
				$this->sourceFlags = $packet->getUnsignedVarInt();
				break;
			case self::SOURCE_CREATIVE:
				break;
			case self::SOURCE_TODO:
				$this->windowId = $packet->getVarInt();
				break;
			default:
				throw new \UnexpectedValueException("Unknown inventory action source type $this->sourceType");
		}

		$this->inventorySlot = $packet->getUnsignedVarInt();
		$this->oldItem = ItemInstance::legacy($packet->getItemStackWithoutStackId());
		$this->newItem = ItemInstance::legacy($packet->getItemStackWithoutStackId());
		if($packet->hasNetworkIds){
			$this->stackNetworkId = $packet->getVarInt();
		}

		if(
			$this->sourceType === self::SOURCE_TODO and (
				$this->windowId === self::SOURCE_TYPE_CRAFTING_RESULT or
				$this->windowId === self::SOURCE_TYPE_CRAFTING_USE_INGREDIENT
			)
		){
			$this->isCraftingPart = true;
			if(!$this->oldItem->stack->isNull() and $this->newItem->stack->isNull()){
				$this->isFinalCraftingPart = true;
			}
		}

		return $this;
	}

	/**
	 * @param NetworkBinaryStream $packet
	 *
	 * @throws \InvalidArgumentException
	 */
	public function write(NetworkBinaryStream $packet) : void{
		if(!$packet instanceof InventoryTransactionPacket428){
			throw new InvalidArgumentException("Expected \$packet to be InventoryTransactionPacket (v428)");
		}

		$packet->putUnsignedVarInt($this->sourceType);

		switch($this->sourceType){
			case self::SOURCE_CONTAINER:
				$packet->putVarInt($this->windowId);
				break;
			case self::SOURCE_WORLD:
				$packet->putUnsignedVarInt($this->sourceFlags);
				break;
			case self::SOURCE_CREATIVE:
				break;
			case self::SOURCE_TODO:
				$packet->putVarInt($this->windowId);
				break;
			default:
				throw new \InvalidArgumentException("Unknown inventory action source type $this->sourceType");
		}

		$packet->putUnsignedVarInt($this->inventorySlot);
		$packet->putItemStackWithoutStackId($this->oldItem->stack);
		$packet->putItemStackWithoutStackId($this->newItem->stack);
		if($packet->hasNetworkIds){
			$packet->putVarInt($this->stackNetworkId);
		}
	}
}
