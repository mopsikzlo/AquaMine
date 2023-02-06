<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\adapter\v408\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\bedrock\adapter\v408\Protocol408Adapter;

class LoginPacket extends \pocketmine\network\bedrock\adapter\v407\protocol\LoginPacket{

	public function mayHaveUnreadBytes() : bool{
		return $this->protocol !== null and $this->protocol !== Protocol408Adapter::PROTOCOL_VERSION;
	}

	public function decodePayload(){
		$this->protocol = $this->getInt();
		if($this->protocol === Protocol408Adapter::PROTOCOL_VERSION){
			$this->decodeConnectionRequest();
		}
	}
}