<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\NetworkSession;

use function count;

class ResourcePackClientResponsePacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::RESOURCE_PACK_CLIENT_RESPONSE_PACKET;

	public const STATUS_REFUSED = 1;
	public const STATUS_SEND_PACKS = 2;
	public const STATUS_HAVE_ALL_PACKS = 3;
	public const STATUS_COMPLETED = 4;

	public $status;
	public $packIds = [];

	public function decodePayload(){
		$this->status = $this->getByte();
		$entryCount = $this->getLShort();
		if($entryCount > 128){
			throw new \UnexpectedValueException("Too many entry count in resource pack response: " . $entryCount);
		}
		while($entryCount-- > 0){
			$this->packIds[] = $this->getString();
		}
	}

	public function encodePayload(){
		$this->putByte($this->status);
		$this->putLShort(count($this->packIds));
		foreach($this->packIds as $id){
			$this->putString($id);
		}
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleResourcePackClientResponse($this);
	}

}
