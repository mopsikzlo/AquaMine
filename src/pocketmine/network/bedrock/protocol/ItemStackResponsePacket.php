<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\bedrock\protocol\types\itemStack\StackResponseContainerInfo;
use pocketmine\network\bedrock\protocol\types\itemStack\StackResponseSlotInfo;
use pocketmine\network\NetworkSession;
use function count;

class ItemStackResponsePacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::ITEM_STACK_RESPONSE_PACKET;

	public const RESULT_OK = 0;
	public const RESULT_ERROR = 1;
	//TODO: there are a ton more possible result types but we don't need them yet and they are wayyyyyy too many for me
	//to waste my time on right now...

	/** @var int */
	public $result;
	/** @var int */
	public $requestId;
	/** @var StackResponseContainerInfo[] */
	public $containerInfo = [];

	public function decodePayload(){
		$this->result = $this->getByte();
		$this->requestId = $this->getVarInt();

		for($i = 0, $count = $this->getUnsignedVarInt(); $i < $count; ++$i){
			$this->containerInfo[] = $this->getStackResponseContainerInfo();
		}
	}

	public function encodePayload(){
		$this->putByte($this->result);
		$this->putVarInt($this->requestId);

		$this->putUnsignedVarInt(count($this->containerInfo));
		foreach($this->containerInfo as $info){
			$this->putStackResponseContainerInfo($info);
		}
	}

	protected function getStackResponseContainerInfo() : StackResponseContainerInfo{
		$info = new StackResponseContainerInfo();
		$info->containerId = $this->getByte();
		for($i = 0, $count = $this->getUnsignedVarInt(); $i < $count; ++$i){
			$info->slotInfo[] = $this->getStackResponseSlotInfo();
		}
		return $info;
	}

	protected function putStackResponseContainerInfo(StackResponseContainerInfo $info) : void{
		$this->putByte($info->containerId);

		$this->putUnsignedVarInt(count($info->slotInfo));
		foreach($info->slotInfo as $info){
			$this->putStackResponseSlotInfo($info);
		}
	}

	protected function getStackResponseSlotInfo() : StackResponseSlotInfo{
		$info = new StackResponseSlotInfo();
		$info->slot = $this->getByte();
		$info->hotbarSlot = $this->getByte();
		$info->count = $this->getByte();
		$info->stackNetworkId = $this->getVarInt();
		$info->customName = $this->getString();
		$info->durabilityCorrection = $this->getVarInt();
		return $info;
	}

	protected function putStackResponseSlotInfo(StackResponseSlotInfo $info) : void{
		$this->putByte($info->slot);
		$this->putByte($info->hotbarSlot);
		$this->putByte($info->count);
		$this->putVarInt($info->stackNetworkId);
		$this->putString($info->customName);
		$this->putVarInt($info->durabilityCorrection);
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleItemStackResponse($this);
	}
}