<?php

declare(strict_types=1);

namespace pocketmine\item;

class NetherQuartz extends Item{
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::NETHER_QUARTZ, $meta, $count, "Nether Quartz");
	}

}