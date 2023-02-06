<?php

declare(strict_types=1);

namespace pocketmine\item;


class GoldPickaxe extends Pickaxe{
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::GOLD_PICKAXE, $meta, $count, "Gold Pickaxe", self::TIER_GOLD);
	}
}
