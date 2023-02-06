<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol\types;

use pocketmine\network\bedrock\protocol\DataPacket;
use pocketmine\network\mcpe\NetworkBinaryStream;
use pocketmine\network\bedrock\protocol\InventoryTransactionPacket;

class NormalTransactionData extends TransactionData{

	public function getTypeId() : int{
		return InventoryTransactionPacket::TYPE_NORMAL;
	}

	protected function decodeData(DataPacket $stream) : void{

	}

	protected function encodeData(DataPacket $stream) : void{

	}

	/**
	 * @param NetworkInventoryAction[] $actions
	 *
	 * @return NormalTransactionData
	 */
	public static function new(array $actions) : self{
		$result = new self();
		$result->actions = $actions;
		return $result;
	}
}
