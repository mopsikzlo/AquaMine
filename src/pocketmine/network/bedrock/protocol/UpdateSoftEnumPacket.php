<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\NetworkSession;
use function count;

class UpdateSoftEnumPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::UPDATE_SOFT_ENUM_PACKET;

	public const TYPE_ADD = 0;
	public const TYPE_REMOVE = 1;
	public const TYPE_SET = 2;

	/** @var string */
	public $enumName;
	/** @var string[] */
	public $values = [];
	/** @var int */
	public $type;

	public function decodePayload(){
		$this->enumName = $this->getString();
		for($i = 0, $count = $this->getUnsignedVarInt(); $i < $count; ++$i){
			$this->values[] = $this->getString();
		}
		$this->type = $this->getByte();
	}

	public function encodePayload(){
		$this->putString($this->enumName);
		$this->putUnsignedVarInt(count($this->values));
		foreach($this->values as $v){
			$this->putString($v);
		}
		$this->putByte($this->type);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleUpdateSoftEnum($this);
	}
}
