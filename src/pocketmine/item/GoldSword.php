<?php

declare(strict_types=1);

namespace pocketmine\item;


class GoldSword extends Sword{
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::GOLD_SWORD, $meta, $count, "Gold Sword", self::TIER_GOLD);
	}
}
