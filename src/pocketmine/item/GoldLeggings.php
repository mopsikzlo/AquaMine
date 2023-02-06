<?php

declare(strict_types=1);

namespace pocketmine\item;


class GoldLeggings extends Armor{
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::GOLD_LEGGINGS, $meta, $count, "Gold Leggings");
	}

	public function getArmorPoints() : int{
		return 3;
	}

	public function getMaxDurability() : int{
		return 106;
	}
}