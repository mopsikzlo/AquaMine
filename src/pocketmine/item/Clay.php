<?php

declare(strict_types=1);

namespace pocketmine\item;

class Clay extends Item{
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::CLAY, $meta, $count, "Clay");
	}

}

