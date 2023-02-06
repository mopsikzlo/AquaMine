<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\adapter\v419\protocol;

#include <rules/DataPacket.h>


class LoginPacket extends \pocketmine\network\bedrock\adapter\v407\protocol\LoginPacket{

	public function mayHaveUnreadBytes() : bool{
		return $this->protocol !== null and $this->protocol !== ProtocolInfo::CURRENT_PROTOCOL;
	}

	public function decodePayload(){
		$this->protocol = $this->getInt();
		if($this->protocol === ProtocolInfo::CURRENT_PROTOCOL){
			$this->decodeConnectionRequest();
		}
	}
}