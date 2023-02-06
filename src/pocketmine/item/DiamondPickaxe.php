<?php

declare(strict_types=1);

namespace pocketmine\item;


class DiamondPickaxe extends Pickaxe {
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::DIAMOND_PICKAXE, $meta, $count, "Diamond Pickaxe", self::TIER_DIAMOND);
	}
}
