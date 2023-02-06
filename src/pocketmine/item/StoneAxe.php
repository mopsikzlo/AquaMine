<?php

declare(strict_types=1);

namespace pocketmine\item;


class StoneAxe extends Axe{
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::STONE_AXE, $meta, $count, "Stone Axe", self::TIER_STONE);
	}
}