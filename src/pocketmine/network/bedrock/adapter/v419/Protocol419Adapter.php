<?php
declare(strict_types=1);

namespace pocketmine\network\bedrock\adapter\v419;

use pocketmine\network\bedrock\adapter\v419\protocol as protocol419;
use pocketmine\network\bedrock\adapter\v422\Protocol422Adapter;
use pocketmine\network\bedrock\protocol\DataPacket;
use pocketmine\network\bedrock\protocol\FilterTextPacket;
use pocketmine\network\bedrock\protocol\ItemStackResponsePacket;
use pocketmine\network\bedrock\protocol\ProtocolInfo;
use pocketmine\network\bedrock\protocol\ResourcePacksInfoPacket;
use pocketmine\utils\Binary;

class Protocol419Adapter extends Protocol422Adapter{

	public function processClientToServer(string $buf) : ?DataPacket{
		$offset = 0;
		$pid = Binary::readUnsignedVarInt($buf, $offset);
		switch($pid){
			case ProtocolInfo::FILTER_TEXT_PACKET:
				return null;
			case protocol419\ProtocolInfo::LOGIN_PACKET:
				return new protocol419\LoginPacket($buf);
			case protocol419\ProtocolInfo::RESOURCE_PACKS_INFO_PACKET:
				return new protocol419\ResourcePacksInfoPacket($buf);
			case protocol419\ProtocolInfo::ITEM_STACK_RESPONSE_PACKET:
				return new protocol419\ItemStackResponsePacket($buf);
		}

		return parent::processClientToServer($buf);
	}

	public function processServerToClient(DataPacket $packet) : ?DataPacket{
		if($packet instanceof FilterTextPacket){
			return null;
		}elseif($packet instanceof ResourcePacksInfoPacket){
			$pk = new protocol419\ResourcePacksInfoPacket();
			$pk->mustAccept = $packet->mustAccept;
			$pk->hasScripts = $packet->hasScripts;
			$pk->behaviorPackEntries = $packet->behaviorPackEntries;
			$pk->resourcePackEntries = $packet->resourcePackEntries;
			return $pk;
		}elseif($packet instanceof ItemStackResponsePacket){
			$pk = new protocol419\ItemStackResponsePacket();
			$pk->result = $packet->result;
			$pk->requestId = $packet->requestId;
			$pk->containerInfo = $packet->containerInfo;
			return $pk;
		}

		return parent::processServerToClient($packet);
	}

	public function getProtocolVersion() : int{
		return protocol419\ProtocolInfo::CURRENT_PROTOCOL;
	}
}