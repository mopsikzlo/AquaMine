<?php

declare(strict_types=1);

namespace pocketmine\item;


class DiamondAxe extends Axe{
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::DIAMOND_AXE, $meta, $count, "Diamond Axe", self::TIER_DIAMOND);
	}
}