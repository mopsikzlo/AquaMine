<?php

declare(strict_types=1);

namespace pocketmine\item;


class StoneShovel extends Pickaxe{
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::STONE_SHOVEL, $meta, $count, "Stone Shovel", self::TIER_STONE);
	}
}
