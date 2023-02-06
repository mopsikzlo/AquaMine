<?php

declare(strict_types=1);

namespace pocketmine\item;


class IronAxe extends Axe {
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::IRON_AXE, $meta, $count, "Iron Axe", self::TIER_IRON);
	}
}