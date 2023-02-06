<?php

declare(strict_types=1);

namespace pocketmine\event\explosion;

use pocketmine\event\Event;
use pocketmine\level\Explosion;

abstract class ExplosionEvent extends Event{

	/** @var Explosion */
	protected $explosion;

	public function __construct(Explosion $explosion){
		$this->explosion = $explosion;
	}

	/**
	 * @return Explosion
	 */
	public function getExplosion() : Explosion{
		return $this->explosion;
	}
}