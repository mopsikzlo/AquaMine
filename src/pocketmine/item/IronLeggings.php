<?php

declare(strict_types=1);

namespace pocketmine\item;


class IronLeggings extends Armor{
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::IRON_LEGGINGS, $meta, $count, "Iron Leggings");
	}

	public function getArmorPoints() : int{
		return 5;
	}

	public function getArmorHash() : int{
		return $this->getIronArmorPoints();
	}

	public function getMaxDurability() : int{
		return 226;
	}
}