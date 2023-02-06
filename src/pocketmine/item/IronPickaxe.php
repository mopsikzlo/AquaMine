<?php

declare(strict_types=1);

namespace pocketmine\item;


class IronPickaxe extends Pickaxe {
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::IRON_PICKAXE, $meta, $count, "Iron Pickaxe", self::TIER_IRON);
	}
}