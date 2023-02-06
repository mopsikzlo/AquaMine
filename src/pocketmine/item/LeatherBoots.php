<?php

declare(strict_types=1);

namespace pocketmine\item;


class LeatherBoots extends Armor{
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::LEATHER_BOOTS, $meta, $count, "Leather Boots");
	}

	public function getArmorPoints() : int{
		return 1;
	}

	public function getArmorHash() : int{
		return $this->getLeatherArmorPoints();
	}

	public function getMaxDurability() : int{
		return 66;
	}
}