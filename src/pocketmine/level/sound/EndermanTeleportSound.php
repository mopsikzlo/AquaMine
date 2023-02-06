<?php

declare(strict_types=1);

namespace pocketmine\level\sound;

use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\LevelEventPacket;

class EndermanTeleportSound extends GenericSound{
	public function __construct(Vector3 $pos){
		parent::__construct($pos, LevelEventPacket::EVENT_SOUND_ENDERMAN_TELEPORT);
	}
}
