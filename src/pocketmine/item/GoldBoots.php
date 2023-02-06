<?php

declare(strict_types=1);

namespace pocketmine\item;


class GoldBoots extends Armor{
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::GOLD_BOOTS, $meta, $count, "Gold Boots");
	}

	public function getArmorPoints() : int{
		return 1;
	}

	public function getMaxDurability() : int{
		return 92;
	}
}