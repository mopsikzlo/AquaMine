<?php

declare(strict_types=1);

namespace pocketmine\nbt\tag;

abstract class ImmutableTag extends Tag{

	protected function makeCopy(){
		return $this; //immutable types don't need to be copied
	}
}
