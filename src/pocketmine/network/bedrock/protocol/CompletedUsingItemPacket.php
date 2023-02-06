<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\NetworkSession;

class CompletedUsingItemPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::COMPLETED_USING_ITEM_PACKET;

	public const ACTION_UNKNOWN = -1;
	public const ACTION_EQUIP_ARMOR = 0;
	public const ACTION_EAT = 1;
	public const ACTION_ATTACK = 2;
	public const ACTION_CONSUME = 3;
	public const ACTION_THROW = 4;
	public const ACTION_SHOOT = 5;
	public const ACTION_PLACE = 6;
	public const ACTION_FILL_BOTTLE = 7;
	public const ACTION_FILL_BUCKET = 8;
	public const ACTION_POUR_BUCKET = 9;
	public const ACTION_USE_TOOL = 10;
	public const ACTION_INTERACT = 11;
	public const ACTION_RETRIEVE = 12;
	public const ACTION_DYED = 13;
	public const ACTION_TRADED = 14;

	/** @var int */
	public $itemId;
	/** @var int */
	public $action;

	public function decodePayload(){
		$this->itemId = $this->getLShort();
		$this->action = $this->getLInt();
	}

	public function encodePayload(){
		$this->putLShort($this->itemId);
		$this->putLInt($this->action);
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleCompletedUsingItem($this);
	}
}