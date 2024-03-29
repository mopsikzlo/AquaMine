<?php

declare(strict_types=1);

namespace pocketmine\utils;

class ReversePriorityQueue extends \SplPriorityQueue{

	public function compare($priority1, $priority2){
		return (int) -($priority1 - $priority2);
	}
}