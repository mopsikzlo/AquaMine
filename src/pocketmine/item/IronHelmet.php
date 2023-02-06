<?php

declare(strict_types=1);

namespace pocketmine\item;


class IronHelmet extends Armor{
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::IRON_HELMET, $meta, $count, "Iron Helmet");
	}

	public function getArmorPoints() : int{
		return 2;
	}

	public function getArmorHash() : int{
		return $this->getIronArmorPoints();
	}

	public function getMaxDurability() : int{
		return 166;
	}
}