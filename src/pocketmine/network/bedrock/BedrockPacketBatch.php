<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock;

use pocketmine\network\bedrock\protocol\DataPacket;
use pocketmine\network\bedrock\protocol\PacketPool;
use pocketmine\network\mcpe\NetworkBinaryStream;
use pocketmine\utils\BinaryDataException;

class BedrockPacketBatch extends NetworkBinaryStream{

	public function putPacket(DataPacket $packet) : void{
		if(!$packet->isEncoded){
			$packet->encode();
		}
		$this->putString($packet->getBuffer());
	}

	/**
	 * @return DataPacket
	 * @throws BinaryDataException
	 */
	public function getPacket() : DataPacket{
		return PacketPool::getPacket($this->getString());
	}

	/**
	 * Constructs a packet batch from the given list of packets.
	 *
	 * @param Packet ...$packets
	 *
	 * @return PacketBatch
	 */
	public static function fromPackets(DataPacket ...$packets) : BedrockPacketBatch{
		$result = new BedrockPacketBatch();
		foreach($packets as $packet){
			$result->putPacket($packet);
		}
		return $result;
	}
}
