<?php

declare(strict_types=1);

namespace pocketmine\item;


class DiamondSword extends Sword {
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::DIAMOND_SWORD, $meta, $count, "Diamond Sword", self::TIER_DIAMOND);
	}
}
