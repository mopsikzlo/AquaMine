<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\bedrock\protocol\types\skin\Skin;
use pocketmine\network\NetworkSession;
use pocketmine\utils\UUID;

class PlayerSkinPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::PLAYER_SKIN_PACKET;

	/** @var UUID */
	public $uuid;
	/** @var Skin */
	public $skin;
	/** @var string */
	public $oldSkinName = "";
	/** @var string */
	public $newSkinName = "";

	public function decodePayload(){
		$this->uuid = $this->getUUID();
		$this->skin = $this->getSkin();
		$this->newSkinName = $this->getString();
		$this->oldSkinName = $this->getString();

		$verified = $this->getBool();
		$this->skin->setVerified($verified);
	}

	public function encodePayload(){
		$this->putUUID($this->uuid);
		$this->putSkin($this->skin);
		$this->putString($this->newSkinName);
		$this->putString($this->oldSkinName);
		$this->putBool($this->skin->isVerified());
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handlePlayerSkin($this);
	}
}
