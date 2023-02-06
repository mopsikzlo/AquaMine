<?php

declare(strict_types=1);

namespace pocketmine\item;


class GoldHoe extends Hoe{
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::GOLD_HOE, $meta, $count, "Gold Hoe", self::TIER_GOLD);
	}
}