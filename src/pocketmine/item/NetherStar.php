<?php

declare(strict_types=1);

namespace pocketmine\item;


class NetherStar extends Item{
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::NETHER_STAR, $meta, $count, "Nether Star");
	}
}