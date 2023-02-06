<?php

declare(strict_types=1);

namespace pocketmine\item;


class WoodenShovel extends Shovel{
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::WOODEN_SHOVEL, $meta, $count, "Wooden Shovel", self::TIER_WOODEN);
	}
}
