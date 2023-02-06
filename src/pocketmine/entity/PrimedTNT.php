<?php

declare(strict_types=1);

namespace pocketmine\entity;

use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\ExplosionPrimeEvent;
use pocketmine\level\Explosion;
use pocketmine\network\mcpe\protocol\LevelEventPacket;

class PrimedTNT extends Entity implements Explosive{
	public const NETWORK_ID = self::TNT;

	public $width = 0.98;
	public $height = 0.98;

	protected $baseOffset = 0.49;

	public $gravity = 0.04;
	public $drag = 0.02;

	protected $fuse;

	public $canCollide = false;


	public function attack($damage, EntityDamageEvent $source){
		if($source->getCause() === EntityDamageEvent::CAUSE_VOID){
			parent::attack($damage, $source);
		}
	}

	protected function initEntity(){
		parent::initEntity();

		$this->fuse = $this->namedtag->getByte("Fuse", 80);

		$this->setDataFlag(self::DATA_FLAGS, self::DATA_FLAG_IGNITED, true);
		$this->setDataProperty(self::DATA_FUSE_LENGTH, self::DATA_TYPE_INT, $this->fuse);

		$this->level->broadcastLevelEvent($this, LevelEventPacket::EVENT_SOUND_IGNITE);
	}


	public function canCollideWith(Entity $entity){
		return false;
	}

	public function saveNBT(){
		parent::saveNBT();
		$this->namedtag->setByte("Fuse", $this->fuse);
	}

	public function entityBaseTick($tickDiff = 1){
		if($this->closed){
			return false;
		}

		$hasUpdate = parent::entityBaseTick($tickDiff);

		if($this->fuse % 5 === 0){ //don't spam it every tick, it's not necessary
			$this->setDataProperty(self::DATA_FUSE_LENGTH, self::DATA_TYPE_INT, $this->fuse);
		}

		if(!$this->isFlaggedForDespawn()){
			$this->fuse -= $tickDiff;

			if($this->fuse <= 0){
				$this->flagForDespawn();
				$this->explode();
			}
		}

		return $hasUpdate or $this->fuse >= 0;
	}

	public function explode(){
		$ev = new ExplosionPrimeEvent($this, 4);
		$ev->call();

		if(!$ev->isCancelled()){
			$explosion = new Explosion($this, $ev->getForce(), $this);
			if($ev->isBlockBreaking()){
				$explosion->explodeA();
			}
			$explosion->explodeB();
		}
	}
}
