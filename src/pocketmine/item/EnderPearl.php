<?php

declare(strict_types=1);

namespace pocketmine\item;

class EnderPearl extends ProjectileItem{

	/**
	 * @param int $meta
	 * @param int $count
	 */
	public function __construct($meta = 0, $count = 1){
		parent::__construct(Item::ENDER_PEARL, $meta, $count, "Ender Pearl");
	}

	/**
	 * @return int
	 */
	public function getMaxStackSize() : int{
		return 16;
	}

	public function getCooldownTicks() : int{
		return 20;
	}

	public function getProjectileEntityType() : string{
		return "EnderPearl";
	}

	public function getThrowForce() : float{
		return 1.5;
	}
}
