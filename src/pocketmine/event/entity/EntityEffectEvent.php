<?php

declare(strict_types=1);

namespace pocketmine\event\entity;

use pocketmine\entity\Effect;
use pocketmine\entity\Entity;
use pocketmine\event\Cancellable;

class EntityEffectEvent extends EntityEvent implements Cancellable{

	/** @var Effect */
	private $effect;

	public function __construct(Entity $entity, Effect $effect){
		$this->entity = $entity;
		$this->effect = $effect;
	}

	public function getEffect() : Effect{
		return $this->effect;
	}
}