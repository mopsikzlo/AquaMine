<?php

declare(strict_types=1);

namespace pocketmine\level\sound;

use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\DataPacket;

abstract class Sound extends Vector3{

	/**
	 * @return DataPacket|DataPacket[]
	 */
	abstract public function encode();

}
