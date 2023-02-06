<?php

declare(strict_types=1);

namespace pocketmine\item;

use pocketmine\entity\Effect;

class GoldenAppleEnchanted extends GoldenApple{

	public function __construct($meta = 0, $count = 1){
		Food::__construct(self::ENCHANTED_GOLDEN_APPLE, $meta, $count, "Enchanted Golden Apple"); //skip parent constructor
	}

	public function getAdditionalEffects() : array{
		return [
			Effect::getEffect(Effect::REGENERATION)->setDuration(600)->setAmplifier(4),
			Effect::getEffect(Effect::ABSORPTION)->setDuration(2400)->setAmplifier(3),
			Effect::getEffect(Effect::DAMAGE_RESISTANCE)->setDuration(6000),
			Effect::getEffect(Effect::FIRE_RESISTANCE)->setDuration(6000),
		];
	}
}
