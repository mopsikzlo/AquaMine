<?php

declare(strict_types=1);

namespace pocketmine\item;


class DiamondChestplate extends Armor{
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::DIAMOND_CHESTPLATE, $meta, $count, "Diamond Chestplate");
	}

	public function getArmorPoints() : int{
		return 8;
	}

	public function getArmorHash() : int{
		return $this->getDiamondArmorPoints();
	}

	public function getMaxDurability() : int{
		return 529;
	}
}