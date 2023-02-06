<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\adapter\v428\protocol;

use pocketmine\network\bedrock\adapter\v428\protocol\types\MismatchTransactionData;
use pocketmine\network\bedrock\adapter\v428\protocol\types\NormalTransactionData;
use pocketmine\network\bedrock\adapter\v428\protocol\types\ReleaseItemTransactionData;
use pocketmine\network\bedrock\adapter\v428\protocol\types\UseItemOnActorTransactionData;
use pocketmine\network\bedrock\adapter\v428\protocol\types\UseItemTransactionData;

class InventoryTransactionPacket extends \pocketmine\network\bedrock\protocol\InventoryTransactionPacket{
	use PacketTrait;

	/** @var bool */
	public $hasNetworkIds;

	public function decodePayload(){
		$this->legacyRequestId = $this->getVarInt();
		if($this->legacyRequestId !== 0){
			for($i = 0, $count = $this->getUnsignedVarInt(); $i < $count; ++$i){
				$this->legacySetItemSlots[] = $this->getLegacySetItemSlot();
			}
		}
		$transactionType = $this->getUnsignedVarInt();
		switch($transactionType){
			case self::TYPE_NORMAL:
				$this->trData = new NormalTransactionData();
				break;
			case self::TYPE_MISMATCH:
				$this->trData = new MismatchTransactionData();
				break;
			case self::TYPE_USE_ITEM:
				$this->trData = new UseItemTransactionData();
				break;
			case self::TYPE_USE_ITEM_ON_ACTOR:
				$this->trData = new UseItemOnActorTransactionData();
				break;
			case self::TYPE_RELEASE_ITEM:
				$this->trData = new ReleaseItemTransactionData();
				break;
			default:
				throw new \UnexpectedValueException("Unknown transaction type $transactionType");
		}

		$this->hasNetworkIds = $this->getBool();
		$this->trData->decode($this);
	}

	public function encodePayload(){
		$this->putVarInt($this->legacyRequestId);
		if($this->legacyRequestId !== 0){
			$this->putUnsignedVarInt(count($this->legacySetItemSlots));
			foreach($this->legacySetItemSlots as $setItemSlot){
				$this->putLegacySetItemSlot($setItemSlot);
			}
		}

		$this->putBool($this->hasNetworkIds);

		$this->putUnsignedVarInt($this->trData->getTypeId());
		$this->trData->encode($this);
	}
}