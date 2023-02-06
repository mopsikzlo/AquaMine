<?php

declare(strict_types=1);

namespace pocketmine\item;


class WoodenPickaxe extends Pickaxe {
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::WOODEN_PICKAXE, $meta, $count, "Wooden Pickaxe", self::TIER_WOODEN);
	}
}
