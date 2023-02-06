<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\NetworkSession;

use function count;

class PurchaseReceiptPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::PURCHASE_RECEIPT_PACKET;

	/** @var string[] */
	public $entries = [];

	public function decodePayload(){
		$count = $this->getUnsignedVarInt();
		for($i = 0; $i < $count; ++$i){
			$this->entries[] = $this->getString();
		}
	}

	public function encodePayload(){
		$this->putUnsignedVarInt(count($this->entries));
		foreach($this->entries as $entry){
			$this->putString($entry);
		}
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handlePurchaseReceipt($this);
	}
}