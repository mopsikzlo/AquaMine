<?php

declare(strict_types=1);

namespace pocketmine\item;

class StringItem extends Item{
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::STRING, $meta, $count, "String");
	}

}

