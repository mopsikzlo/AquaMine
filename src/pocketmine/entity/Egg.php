<?php

declare(strict_types=1);

namespace pocketmine\entity;

use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\item\Item;
use pocketmine\level\particle\ItemBreakParticle;
use pocketmine\network\mcpe\protocol\AddEntityPacket;
use pocketmine\Player;

class Egg extends Throwable{
	public const NETWORK_ID = self::EGG;

	protected function onHit(ProjectileHitEvent $event) : void{
		for($i = 0; $i < 6; ++$i){
			$this->level->addParticle(new ItemBreakParticle($this, Item::get(Item::EGG)));
		}
	}
}
