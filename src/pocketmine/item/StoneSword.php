<?php

declare(strict_types=1);

namespace pocketmine\item;


class StoneSword extends Sword {
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::STONE_SWORD, $meta, $count, "Stone Sword", self::TIER_STONE);
	}
}
