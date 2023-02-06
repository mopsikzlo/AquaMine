<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe;

use pocketmine\network\mcpe\protocol\DataPacket;
use pocketmine\network\mcpe\protocol\PacketPool;
use pocketmine\utils\BinaryDataException;

class MCPEPacketBatch extends NetworkBinaryStream{

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
	 * @param DataPacket ...$packets
	 *
	 * @return MCPEPacketBatch
	 */
	public static function fromPackets(DataPacket ...$packets) : MCPEPacketBatch{
		$result = new MCPEPacketBatch();
		foreach($packets as $packet){
			$result->putPacket($packet);
		}
		return $result;
	}
}
