<?php

declare(strict_types=1);

namespace pocketmine\level\particle;

use pocketmine\item\Item;
use pocketmine\math\Vector3;

class ItemBreakParticle extends GenericParticle{
	public function __construct(Vector3 $pos, Item $item){
		parent::__construct($pos, Particle::TYPE_ITEM_BREAK, ($item->getId() << 16) | $item->getDamage());
	}
}
