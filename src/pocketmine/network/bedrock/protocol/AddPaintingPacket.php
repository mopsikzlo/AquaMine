<?php

/*
 *
 *  ____	    _	_   __  __ _		  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
*/

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>


use pocketmine\math\Vector3;
use pocketmine\network\NetworkSession;

class AddPaintingPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::ADD_PAINTING_PACKET;

	/** @var int|null */
	public $actorUniqueId = null;
	/** @var int */
	public $actorRuntimeId;
	/** @var Vector3 */
	public $position;
	/** @var int */
	public $direction;
	/** @var string */
	public $title;

	public function decodePayload(){
		$this->actorUniqueId = $this->getActorUniqueId();
		$this->actorRuntimeId = $this->getActorRuntimeId();
		$this->position = $this->getVector3();
		$this->title = $this->getString();
	}

	public function encodePayload(){
		$this->putActorUniqueId($this->actorUniqueId ?? $this->actorRuntimeId);
		$this->putActorRuntimeId($this->actorRuntimeId);
		$this->putVector3($this->position);
		$this->putString($this->title);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleAddPainting($this);
	}
}
