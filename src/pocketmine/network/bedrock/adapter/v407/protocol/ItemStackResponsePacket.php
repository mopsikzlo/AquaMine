<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\adapter\v407\protocol;

#include <rules/DataPacket.h>


use function count;

class ItemStackResponsePacket extends \pocketmine\network\bedrock\adapter\v419\protocol\ItemStackResponsePacket{
	use PacketTrait;

	public function decodePayload(){
		$this->result = $this->getBool() ? self::RESULT_OK : self::RESULT_ERROR;
		$this->requestId = $this->getVarInt();

		for($i = 0, $count = $this->getUnsignedVarInt(); $i < $count; ++$i){
			$this->containerInfo[] = $this->getStackResponseContainerInfo();
		}
	}

	public function encodePayload(){
		$this->putBool($this->result === self::RESULT_OK);
		$this->putVarInt($this->requestId);

		$this->putUnsignedVarInt(count($this->containerInfo));
		foreach($this->containerInfo as $info){
			$this->putStackResponseContainerInfo($info);
		}
	}
}