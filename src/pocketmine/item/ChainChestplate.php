<?php

declare(strict_types=1);

namespace pocketmine\item;


class ChainChestplate extends Armor{
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::CHAIN_CHESTPLATE, $meta, $count, "Chain Chestplate");
	}

    public function getArmorPoints() : int{
        return 5;
    }

	public function getMaxDurability() : int{
		return 241;
	}
}