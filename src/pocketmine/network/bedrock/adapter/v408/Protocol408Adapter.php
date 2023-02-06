<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\adapter\v408;

use pocketmine\network\bedrock\adapter\v407\protocol\ProtocolInfo;
use pocketmine\network\bedrock\adapter\v407\Protocol407Adapter;
use pocketmine\network\bedrock\adapter\v408\protocol\LoginPacket as LoginPacket408;
use pocketmine\network\bedrock\protocol\DataPacket;
use pocketmine\utils\Binary;

class Protocol408Adapter extends Protocol407Adapter{

	public const PROTOCOL_VERSION = 408;

	public function processClientToServer(string $buf) : ?DataPacket{
		$offset = 0;
		$pid = Binary::readUnsignedVarInt($buf, $offset);
		if($pid === ProtocolInfo::LOGIN_PACKET){
			return new LoginPacket408($buf);
		}

		return parent::processClientToServer($buf);
	}

	public function getProtocolVersion() : int{
		return self::PROTOCOL_VERSION;
	}
}