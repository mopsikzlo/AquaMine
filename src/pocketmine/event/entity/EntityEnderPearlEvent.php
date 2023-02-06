<?php

declare(strict_types=1);

namespace pocketmine\event\entity;

use pocketmine\entity\EnderPearl;
use pocketmine\entity\Entity;
use pocketmine\event\Cancellable;
use pocketmine\math\Vector3;

class EntityEnderPearlEvent extends EntityEvent implements Cancellable{
	public static $handlerList = null;

	/** @var EnderPearl */
	private $projectile;
	/** @var Vector3 */
	private $to;

	public function __construct(Entity $entity, EnderPearl $projectile, Vector3 $to){
		$this->entity = $entity;
		$this->projectile = $projectile;
	}

	/**
	 * @return EnderPearl
	 */
	public function getEnderPearl() : EnderPearl{
		return $this->projectile;
	}

	/**
	 * @return Vector3
	 */
	public function getTo() : Vector3{
		return $this->to;
	}
}