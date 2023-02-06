<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>

use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\network\bedrock\protocol\types\inventory\ItemInstance;
use pocketmine\network\NetworkSession;
use pocketmine\network\bedrock\protocol\types\actor\ActorLink;
use pocketmine\Player;
use pocketmine\utils\UUID;
use function count;

class AddPlayerPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::ADD_PLAYER_PACKET;

	/** @var UUID */
	public $uuid;
	/** @var string */
	public $username;
	/** @var int|null */
	public $actorUniqueId = null; //TODO
	/** @var int */
	public $actorRuntimeId;
	/** @var string */
	public $platformChatId = "";
	/** @var Vector3 */
	public $position;
	/** @var Vector3|null */
	public $motion;
	/** @var float */
	public $pitch = 0.0;
	/** @var float */
	public $yaw = 0.0;
	/** @var float|null */
	public $headYaw = null; //TODO
	/** @var ItemInstance */
	public $item;
	/** @var int */
	public $gameMode = Player::SURVIVAL;
	/** @var array */
	public $metadata = [];

	//TODO: adventure settings stuff
	public $uvarint1 = 0;
	public $uvarint2 = 0;
	public $uvarint3 = 0;
	public $uvarint4 = 0;
	public $uvarint5 = 0;

	public $long1 = 0;

	/** @var ActorLink[] */
	public $links = [];

	/** @var string */
	public $deviceId = ""; //TODO: fill player's device ID (???)
	/** @var int */
	public $deviceOS = -1; //TODO: fill player's device OS

	public function decodePayload(){
		$this->uuid = $this->getUUID();
		$this->username = $this->getString();
		$this->actorUniqueId = $this->getActorUniqueId();
		$this->actorRuntimeId = $this->getActorRuntimeId();
		$this->platformChatId = $this->getString();
		$this->position = $this->getVector3();
		$this->motion = $this->getVector3();
		$this->pitch = $this->getLFloat();
		$this->yaw = $this->getLFloat();
		$this->headYaw = $this->getLFloat();
		$this->item = $this->getItemInstance();
		$this->gameMode = $this->getVarInt();
		$this->metadata = $this->getActorMetadata();

		$this->uvarint1 = $this->getUnsignedVarInt();
		$this->uvarint2 = $this->getUnsignedVarInt();
		$this->uvarint3 = $this->getUnsignedVarInt();
		$this->uvarint4 = $this->getUnsignedVarInt();
		$this->uvarint5 = $this->getUnsignedVarInt();

		$this->long1 = $this->getLLong();

		$linkCount = $this->getUnsignedVarInt();
		for($i = 0; $i < $linkCount; ++$i){
			$this->links[$i] = $this->getActorLink();
		}

		$this->deviceId = $this->getString();
		$this->deviceOS = $this->getLInt();
	}

	public function encodePayload(){
		$this->putUUID($this->uuid);
		$this->putString($this->username);
		$this->putActorUniqueId($this->actorUniqueId ?? $this->actorRuntimeId);
		$this->putActorRuntimeId($this->actorRuntimeId);
		$this->putString($this->platformChatId);
		$this->putVector3($this->position);
		$this->putVector3Nullable($this->motion);
		$this->putLFloat($this->pitch);
		$this->putLFloat($this->yaw);
		$this->putLFloat($this->headYaw ?? $this->yaw);
		$this->putItemInstance($this->item);
		$this->putVarInt($this->gameMode);
		$this->putActorMetadata($this->metadata);

		$this->putUnsignedVarInt($this->uvarint1);
		$this->putUnsignedVarInt($this->uvarint2);
		$this->putUnsignedVarInt($this->uvarint3);
		$this->putUnsignedVarInt($this->uvarint4);
		$this->putUnsignedVarInt($this->uvarint5);

		$this->putLLong($this->long1);

		$this->putUnsignedVarInt(count($this->links));
		foreach($this->links as $link){
			$this->putActorLink($link);
		}

		$this->putString($this->deviceId);
		$this->putLInt($this->deviceOS);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleAddPlayer($this);
	}
}
