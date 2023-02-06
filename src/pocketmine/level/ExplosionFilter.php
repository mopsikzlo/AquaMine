<?php

declare(strict_types=1);

namespace pocketmine\level;

use pocketmine\block\Block;
use pocketmine\entity\Entity;

interface ExplosionFilter{

	public function canAffectBlock(Block $block) : bool;

	public function canAffectEntity(Entity $entity) : bool;
}