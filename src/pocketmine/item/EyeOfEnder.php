<?php

declare(strict_types=1);

namespace pocketmine\item;

class EyeOfEnder extends Item{

	/**
	 * @param int $meta
	 * @param int $count
	 */
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::EYE_OF_ENDER, 0, $count, "Eye Of Ender");
	}

}