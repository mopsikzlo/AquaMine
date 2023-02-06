<?php

declare(strict_types=1);

namespace pocketmine\item;


class LeatherPants extends Armor{
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::LEATHER_PANTS, $meta, $count, "Leather Pants");
	}

	public function getArmorPoints() : int{
		return 2;
	}

	public function getArmorHash() : int{
		return $this->getLeatherArmorPoints();
	}

	public function getMaxDurability() : int{
		return 76;
	}
}