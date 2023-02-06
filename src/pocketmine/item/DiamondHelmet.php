<?php

declare(strict_types=1);

namespace pocketmine\item;


class DiamondHelmet extends Armor{
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::DIAMOND_HELMET, $meta, $count, "Diamond Helmet");
	}

	public function getArmorPoints() : int{
		return 3;
	}

	public function getArmorHash() : int{
		return $this->getDiamondArmorPoints();
	}

	public function getMaxDurability() : int{
		return 364;
	}
}