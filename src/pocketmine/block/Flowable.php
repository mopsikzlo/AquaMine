<?php

declare(strict_types=1);

namespace pocketmine\block;

abstract class Flowable extends Transparent{

	public function canBeFlowedInto(){
		return true;
	}

	public function getHardness(){
		return 0;
	}

	public function getBlastResistance() : float{
		return 0;
	}

	public function isSolid(){
		return false;
	}

	protected function recalculateBoundingBox(){
		return null;
	}
}