<?php

declare(strict_types=1);

namespace pocketmine\item;


class ChainBoots extends Armor{
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::CHAIN_BOOTS, $meta, $count, "Chainmail Boots");
	}

    public function getArmorPoints() : int{
        return 1;
    }

	public function getMaxDurability() : int{
		return 196;
	}
}