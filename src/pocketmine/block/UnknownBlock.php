<?php

declare(strict_types=1);

namespace pocketmine\block;

class UnknownBlock extends Transparent{

	public function getHardness(){
		return 0;
	}
}