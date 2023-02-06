<?php

declare(strict_types=1);

namespace pocketmine\item;


class DiamondLeggings extends Armor{
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::DIAMOND_LEGGINGS, $meta, $count, "Diamond Leggings");
	}

	public function getArmorPoints() : int{
		return 6;
	}

	public function getArmorHash() : int{
		return $this->getDiamondArmorPoints();
	}

	public function getMaxDurability() : int{
		return 496;
	}
}