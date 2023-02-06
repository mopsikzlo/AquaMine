<?php

declare(strict_types=1);

namespace pocketmine\item;

use pocketmine\nbt\tag\CompoundTag;

class SplashPotion extends ProjectileItem{

	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::SPLASH_POTION, $meta, $count, $this->getNameByMeta($meta));
	}

	public function getNameByMeta(int $meta) : string{
		return "Splash " . Potion::getNameByMeta($meta);
	}

	public function getMaxStackSize(){
		return 1;
	}

	public function getProjectileEntityType() : string{
		return "SplashPotion";
	}

	public function getThrowForce() : float{
		return 1.1;
	}

	protected function addExtraTags(CompoundTag $tag) : void{
		$tag->setShort("PotionId", $this->meta);
	}
}