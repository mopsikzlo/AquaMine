<?php

declare(strict_types=1);

namespace pocketmine\block;

class RedstoneTorchUnlit extends Torch{

	protected $id = self::UNLIT_REDSTONE_TORCH;

	public function getName(){
		return "Unlit Redstone Torch";
	}

	public function getLightLevel(){
		return 0;
	}
}
