<?php

declare(strict_types=1);

namespace pocketmine\item;


class DiamondBoots extends Armor{
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::DIAMOND_BOOTS, $meta, $count, "Diamond Boots");
	}

	public function getArmorPoints() : int{
		return 3;
	}

	public function getArmorHash() : int{
		return $this->getDiamondArmorPoints();
	}

	public function getMaxDurability() : int{
		return 430;
	}
}