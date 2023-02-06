<?php

declare(strict_types=1);

namespace pocketmine\item;

use pocketmine\entity\Effect;

use function mt_rand;

class RawChicken extends Food{
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::RAW_CHICKEN, $meta, $count, "Raw Chicken");
	}

	public function getFoodRestore() : int{
		return 2;
	}

	public function getSaturationRestore() : float{
		return 1.2;
	}

	public function getAdditionalEffects() : array{
		return mt_rand(0, 9) < 3 ? [Effect::getEffect(Effect::HUNGER)->setDuration(600)] : [];
	}
}

