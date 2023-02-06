<?php

declare(strict_types=1);

namespace pocketmine\item;


class LeatherCap extends Armor{
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::LEATHER_CAP, $meta, $count, "Leather Cap");
	}

	public function getArmorPoints() : int{
		return 1;
	}

	public function getArmorHash() : int{
		return $this->getLeatherArmorPoints();
	}

	public function getMaxDurability() : int{
		return 56;
	}
}