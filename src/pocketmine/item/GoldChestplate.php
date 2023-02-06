<?php

declare(strict_types=1);

namespace pocketmine\item;


class GoldChestplate extends Armor{
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::GOLD_CHESTPLATE, $meta, $count, "Gold Chestplate");
	}

	public function getArmorPoints() : int{
		return 5;
	}

	public function getMaxDurability() : int{
		return 113;
	}
}