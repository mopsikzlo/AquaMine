<?php

declare(strict_types=1);

namespace pocketmine\item;


class GoldAxe extends Axe {
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::GOLD_AXE, $meta, $count, "Gold Axe", self::TIER_GOLD);
	}
}