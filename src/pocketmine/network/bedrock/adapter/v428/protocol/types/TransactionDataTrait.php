<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\adapter\v428\protocol\types;

use pocketmine\network\bedrock\protocol\InventoryTransactionPacket;
use pocketmine\network\bedrock\adapter\v428\protocol\InventoryTransactionPacket as InventoryTransactionPacket428;


use function count;

trait TransactionDataTrait{
	/** @var NetworkInventoryAction[] */
	protected $actions = [];

	/**
	 * @param InventoryTransactionPacket $packet
	 *
	 * @throws \OutOfBoundsException
	 * @throws \UnexpectedValueException
	 */
	public function decode(InventoryTransactionPacket $packet) : void{
		/** @var InventoryTransactionPacket428 $packet */
		$actionCount = $packet->getUnsignedVarInt();
		if($actionCount > self::MAX_ACTION_COUNT){
			throw new \UnexpectedValueException("Too big action count: $actionCount");
		}
		for($i = 0; $i < $actionCount; ++$i){
			$this->actions[] = (new NetworkInventoryAction())->read($packet);
		}
		$this->decodeData($packet);
	}

	public function encode(InventoryTransactionPacket $packet) : void{
		/** @var InventoryTransactionPacket428 $packet */

		$packet->putUnsignedVarInt(count($this->actions));
		foreach($this->actions as $action){
			$action->write($packet);
		}
		$this->encodeData($packet);
	}

}
