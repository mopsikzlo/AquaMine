<?php

declare(strict_types=1);

namespace pocketmine\item;


class IronShovel extends Shovel {
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::IRON_SHOVEL, $meta, $count, "Iron Shovel", self::TYPE_SHOVEL);
	}
}