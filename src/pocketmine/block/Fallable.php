<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\entity\Entity;
use pocketmine\entity\EntityDataHelper;
use pocketmine\level\Level;
use pocketmine\math\Vector3;

abstract class Fallable extends Solid{

	public function onUpdate($type){
		if($type === Level::BLOCK_UPDATE_NORMAL){
			$down = $this->getSide(Vector3::SIDE_DOWN);
			if($down->getId() === self::AIR or ($down instanceof Liquid)){
				$this->level->setBlock($this, Block::get(Block::AIR), true, true);

				$nbt = EntityDataHelper::createBaseNBT($this->add(0.5, 0, 0.5));
				$nbt->setInt("TileID", $this->getId());
				$nbt->setByte("Data", $this->getDamage());

				$fall = Entity::createEntity("FallingSand", $this->getLevel(), $nbt);

				$fall->spawnToAll();
			}
		}
	}
}