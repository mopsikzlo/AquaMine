<?php

declare(strict_types=1);

namespace pocketmine\item;


class IronChestplate extends Armor{
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::IRON_CHESTPLATE, $meta, $count, "Iron Chestplate");
	}

	public function getArmorPoints() : int{
		return 6;
	}

	public function getArmorHash() : int{
		return $this->getIronArmorPoints();
	}

	public function getMaxDurability() : int{
		return 241;
	}
}