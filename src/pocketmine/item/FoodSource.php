<?php

declare(strict_types=1);

namespace pocketmine\item;

use pocketmine\entity\Effect;

interface FoodSource{
	public function getFoodRestore() : int;

	public function getSaturationRestore() : float;

	public function getResidue();

	/**
	 * @return Effect[]
	 */
	public function getAdditionalEffects() : array;
}
