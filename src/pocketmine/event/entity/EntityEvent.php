<?php

declare(strict_types=1);

/**
 * Entity related Events, like spawn, inventory, attack...
 */
namespace pocketmine\event\entity;

use pocketmine\entity\Entity;
use pocketmine\event\Event;

abstract class EntityEvent extends Event{
	/** @var Entity */
	protected $entity;

	public function getEntity(){
		return $this->entity;
	}
}