<?php

declare(strict_types=1);

namespace pocketmine\item;


class StonePickaxe extends Pickaxe{
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::STONE_PICKAXE, $meta, $count, "Stone Pickaxe", self::TIER_STONE);
	}
}
