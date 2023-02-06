<?php

declare(strict_types=1);

namespace pocketmine\item;


class StoneHoe extends Hoe{
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::STONE_HOE, $meta, $count, "Stone Hoe", self::TIER_STONE);
	}
}