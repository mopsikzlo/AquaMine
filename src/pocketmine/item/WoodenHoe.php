<?php

declare(strict_types=1);

namespace pocketmine\item;


class WoodenHoe extends Hoe{
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::WOODEN_HOE, $meta, $count, "Wooden Hoe", self::TIER_WOODEN);
	}
}