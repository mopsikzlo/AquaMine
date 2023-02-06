<?php

declare(strict_types=1);

namespace pocketmine\block;

class RedstoneTorch extends Torch{

	protected $id = self::LIT_REDSTONE_TORCH;

	public function getName(){
		return "Redstone Torch";
	}

	public function getLightLevel(){
		return 7;
	}
}
