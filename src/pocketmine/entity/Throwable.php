<?php

declare(strict_types=1);

namespace pocketmine\entity;

use pocketmine\block\Block;
use pocketmine\level\Level;
use pocketmine\math\RayTraceResult;
use pocketmine\nbt\tag\CompoundTag;

abstract class Throwable extends Projectile{
	
	public $width = 0.25;
	public $height = 0.25;

	public $gravity = 0.03;
	public $drag = 0.01;

	public function __construct(Level $level, CompoundTag $nbt, Entity $shootingEntity = null){
		parent::__construct($level, $nbt, $shootingEntity);
	}

	public function entityBaseTick($tickDiff = 1){
		if($this->closed){
			return false;
		}

		$hasUpdate = parent::entityBaseTick($tickDiff);

		if($this->age > 1200 or $this->isCollided){
			$this->flagForDespawn();
			$hasUpdate = true;
		}

		return $hasUpdate;
	}

	protected function onHitBlock(Block $blockHit, RayTraceResult $hitResult) : void{
		parent::onHitBlock($blockHit, $hitResult);

		$this->flagForDespawn();
	}
}
