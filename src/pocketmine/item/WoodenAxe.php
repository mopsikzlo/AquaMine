<?php

declare(strict_types=1);

namespace pocketmine\item;


class WoodenAxe extends Axe{
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::WOODEN_AXE, $meta, $count, "Wooden Axe", self::TIER_WOODEN);
	}
}
