<?php

declare(strict_types=1);

namespace pocketmine\item;


class ChainLeggings extends Armor{
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::CHAIN_LEGGINGS, $meta, $count, "Chain Leggings");
	}

    public function getArmorPoints() : int{
        return 4;
    }

	public function getMaxDurability() : int{
		return 226;
	}
}