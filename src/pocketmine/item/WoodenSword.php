<?php

declare(strict_types=1);

namespace pocketmine\item;


class WoodenSword extends Sword {
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::WOODEN_SWORD, $meta, $count, "Wooden Sword", self::TIER_WOODEN);
	}
}
