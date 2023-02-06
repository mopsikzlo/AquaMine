<?php

declare(strict_types=1);

namespace pocketmine\event\entity;

use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\item\FoodSource;

class EntityEatBlockEvent extends EntityEatEvent{
	public function __construct(Entity $entity, FoodSource $foodSource){
		if(!($foodSource instanceof Block)){
			throw new \InvalidArgumentException("Food source must be a block");
		}
		parent::__construct($entity, $foodSource);
	}

	/**
	 * @return Block
	 */
	public function getResidue(){
		return parent::getResidue();
	}

	public function setResidue($residue){
		if(!($residue instanceof Block)){
			throw new \InvalidArgumentException("Eating a Block can only result in a Block residue");
		}
		parent::setResidue($residue);
	}
}
