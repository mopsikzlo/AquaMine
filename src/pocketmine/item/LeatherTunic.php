<?php

declare(strict_types=1);

namespace pocketmine\item;


class LeatherTunic extends Armor{
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::LEATHER_TUNIC, $meta, $count, "Leather Tunic");
	}

	public function getArmorPoints() : int{
		return 3;
	}

	public function getArmorHash() : int{
		return $this->getLeatherArmorPoints();
	}

	public function getMaxDurability() : int{
		return 81;
	}
}