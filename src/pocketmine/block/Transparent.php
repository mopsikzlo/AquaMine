<?php

declare(strict_types=1);

namespace pocketmine\block;


abstract class Transparent extends Block{

	public function isTransparent(){
		return true;
	}

	public function getLightFilter() : int{
		return 0;
	}
}