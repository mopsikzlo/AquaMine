<?php

declare(strict_types=1);

namespace pocketmine\level\particle;

use pocketmine\math\Vector3;

class SnowballPoofParticle extends GenericParticle{
	public function __construct(Vector3 $pos){
		parent::__construct($pos, Particle::TYPE_SNOWBALL_POOF);
	}
}
