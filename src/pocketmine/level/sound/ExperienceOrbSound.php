<?php

namespace pocketmine\level\sound;

use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\LevelEventPacket;

class ExperienceOrbSound extends GenericSound {
	/**
	 * ExperienceOrbSound constructor.
	 *
	 * @param Vector3 $pos
	 * @param int     $pitch
	 */
	public function __construct(Vector3 $pos, $pitch = 0){
		parent::__construct($pos, LevelEventPacket::EVENT_SOUND_ORB, $pitch);
	}
}