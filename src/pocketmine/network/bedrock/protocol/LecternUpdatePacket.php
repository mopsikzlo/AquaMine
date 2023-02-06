<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\NetworkSession;

class LecternUpdatePacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::LECTERN_UPDATE_PACKET;

	/** @var int */
	public $page;
	/** @var int */
	public $totalPages;
	/** @var int */
	public $x;
	/** @var int */
	public $z;
	/** @var int */
	public $y;
	/** @var bool */
	public $dropBook;

	public function decodePayload(){
		$this->page = $this->getByte();
		$this->totalPages = $this->getByte();
		$this->getBlockPosition($this->x, $this->y, $this->z);
		$this->dropBook = $this->getBool();
	}

	public function encodePayload(){
		$this->putByte($this->page);
		$this->putByte($this->totalPages);
		$this->putBlockPosition($this->x, $this->y, $this->z);
		$this->putBool($this->dropBook);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleLecternUpdate($this);
	}
}
