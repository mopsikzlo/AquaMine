<?php

declare(strict_types=1);

namespace pocketmine\item;


class IronSword extends Sword {
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::IRON_SWORD, $meta, $count, "Iron Sword",self::TIER_IRON);
	}
}