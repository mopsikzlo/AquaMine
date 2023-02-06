<?php

declare(strict_types=1);

namespace pocketmine\block;

abstract class Solid extends Block{

	public function isSolid(){
		return true;
	}
}