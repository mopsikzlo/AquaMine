<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>

use pocketmine\entity\Attribute;
use pocketmine\entity\EntityIds;
use pocketmine\math\Vector3;
use pocketmine\network\NetworkSession;
use pocketmine\network\bedrock\protocol\types\actor\ActorLink;
use function array_search;
use function count;

class AddActorPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::ADD_ACTOR_PACKET;

	/** @var int|null */
	public $actorUniqueId = null; //TODO
	/** @var int */
	public $actorRuntimeId;
	/** @var string */
	public $type;
	/** @var Vector3 */
	public $position;
	/** @var Vector3|null */
	public $motion;
	/** @var float */
	public $pitch = 0.0;
	/** @var float */
	public $yaw = 0.0;
	/** @var float */
	public $headYaw = 0.0;

	/** @var Attribute[] */
	public $attributes = [];
	/** @var array */
	public $metadata = [];
	/** @var ActorLink[] */
	public $links = [];

	public function decodePayload(){
		$this->actorUniqueId = $this->getActorUniqueId();
		$this->actorRuntimeId = $this->getActorRuntimeId();
		$this->type = $this->getString();
		$this->position = $this->getVector3();
		$this->motion = $this->getVector3();
		$this->pitch = $this->getLFloat();
		$this->yaw = $this->getLFloat();
		$this->headYaw = $this->getLFloat();

		$attrCount = $this->getUnsignedVarInt();
		for($i = 0; $i < $attrCount; ++$i){
			$name = $this->getString();
			$min = $this->getLFloat();
			$current = $this->getLFloat();
			$max = $this->getLFloat();
			$attr = Attribute::getAttributeByName($name);

			if($attr !== null){
				$attr->setMinValue($min);
				$attr->setMaxValue($max);
				$attr->setValue($current);
				$this->attributes[] = $attr;
			}else{
				throw new \UnexpectedValueException("Unknown attribute type \"$name\"");
			}
		}

		$this->metadata = $this->getActorMetadata();
		$linkCount = $this->getUnsignedVarInt();
		for($i = 0; $i < $linkCount; ++$i){
			$this->links[] = $this->getActorLink();
		}
	}

	public function encodePayload(){
		$this->putActorUniqueId($this->actorUniqueId ?? $this->actorRuntimeId);
		$this->putActorRuntimeId($this->actorRuntimeId);
		$this->putString($this->type);
		$this->putVector3($this->position);
		$this->putVector3Nullable($this->motion);
		$this->putLFloat($this->pitch);
		$this->putLFloat($this->yaw);
		$this->putLFloat($this->headYaw);

		$this->putUnsignedVarInt(count($this->attributes));
		foreach($this->attributes as $attribute){
			$this->putString($attribute->getName());
			$this->putLFloat($attribute->getMinValue());
			$this->putLFloat($attribute->getValue());
			$this->putLFloat($attribute->getMaxValue());
		}

		$this->putActorMetadata($this->metadata);
		$this->putUnsignedVarInt(count($this->links));
		foreach($this->links as $link){
			$this->putActorLink($link);
		}
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleAddActor($this);
	}
}
