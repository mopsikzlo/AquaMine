<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol\types;

use pocketmine\network\bedrock\protocol\DataPacket;
use pocketmine\network\mcpe\NetworkBinaryStream;
use pocketmine\network\bedrock\protocol\InventoryTransactionPacket;
use function count;

class MismatchTransactionData extends TransactionData{

	public function getTypeId() : int{
		return InventoryTransactionPacket::TYPE_MISMATCH;
	}

	protected function decodeData(DataPacket $stream) : void{
		if(!empty($this->actions)){
			throw new \UnexpectedValueException("Mismatch transaction type should not have any actions associated with it, but got " . count($this->actions));
		}
	}

	protected function encodeData(DataPacket $stream) : void{

	}

	public static function new() : self{
		return new self; //no arguments
	}
}
