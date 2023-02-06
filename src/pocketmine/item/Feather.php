<?php

declare(strict_types=1);

namespace pocketmine\item;


class Feather extends Item{
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::FEATHER, $meta, $count, "Feather");
	}

}