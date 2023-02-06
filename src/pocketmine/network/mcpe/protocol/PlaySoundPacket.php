<?php

declare(strict_types=1);


namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\NetworkSession;

class PlaySoundPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::PLAY_SOUND_PACKET;

	/** @var string */
	public $soundName;
	/** @var float */
	public $x;
	/** @var float */
	public $y;
	/** @var float */
	public $z;
	/** @var float */
	public $volume;
	/** @var float */
	public $pitch;

	public function decodePayload(){
		$this->soundName = $this->getString();
		$this->getBlockPosition($this->x, $this->y, $this->z);
		$this->x /= 8;
		$this->y /= 8;
		$this->z /= 8;
		$this->volume = $this->getLFloat();
		$this->pitch = $this->getLFloat();
	}

	public function encodePayload(){
		$this->putString($this->soundName);
		$this->putBlockPosition((int) ($this->x * 8), (int) ($this->y * 8), (int) ($this->z * 8));
		$this->putLFloat($this->volume);
		$this->putLFloat($this->pitch);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handlePlaySound($this);
	}
}