<?php

declare(strict_types=1);

namespace pocketmine\level\particle;

use pocketmine\math\Vector3;

class DustParticle extends ColoredParticle{

	public function __construct(Vector3 $pos, int $r, int $g, int $b, int $a = 255){
		parent::__construct($pos, Particle::TYPE_DUST, $r, $g, $b, $a);
	}
}
