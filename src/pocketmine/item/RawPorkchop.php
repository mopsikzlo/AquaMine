<?php

declare(strict_types=1);

namespace pocketmine\item;

class RawPorkchop extends Item{
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::RAW_PORKCHOP, $meta, $count, "Raw Porkchop");
	}

}

