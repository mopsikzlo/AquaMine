<?php

declare(strict_types=1);

namespace pocketmine\entity;

use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\item\Potion;
use pocketmine\level\Level;
use pocketmine\level\particle\SplashPotionParticle;
use pocketmine\math\RayTraceResult;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ShortTag;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\Player;
use function floor;
use function round;
use function sqrt;

class SplashPotion extends Projectile{
	public const NETWORK_ID = self::SPLASH_POTION;

	public $width = 0.25;
	public $height = 0.25;

	public $gravity = 0.1;
	public $drag = 0.05;

	private $hasSplashed = false;

	/**
	 * SplashPotion constructor.
	 *
	 * @param Level       $level
	 * @param CompoundTag $nbt
	 * @param Entity|null $shootingEntity
	 */
	public function __construct(Level $level, CompoundTag $nbt, Entity $shootingEntity = null){
		if(!$nbt->hasTag("PotionId", ShortTag::class)){
			$nbt->setShort("PotionId", Potion::AWKWARD);
		}

		parent::__construct($level, $nbt, $shootingEntity);
		$this->setDataProperty(self::DATA_POTION_AUX_VALUE, self::DATA_TYPE_SHORT, $this->getPotionId());
	}

	/**
	 * @return int
	 */
	public function getPotionId() : int{
		return $this->namedtag->getShort("PotionId");
	}

	public function splash(){
		if(!$this->hasSplashed){
			$this->hasSplashed = true;

			$color = [0x38, 0x5d, 0xc6];
			$effect = Potion::getEffectByMeta($this->getPotionId());
			if($effect !== null){
				$color = $effect->getColor();
			}

			$this->getLevel()->addParticle(new SplashPotionParticle($this, $color[0], $color[1], $color[2]));
			$this->getLevel()->broadcastLevelSoundEvent($this, LevelSoundEventPacket::SOUND_GLASS);

			if($effect !== null){
				foreach($this->getLevel()->getNearbyEntities($this->getBoundingBox()->grow(4.125, 2.125, 4.125)) as $e){
					if($e instanceof Living and !($e instanceof Player and $e->isSpectator())){
						$distanceSquared = $e->distanceSquared($this);
						if($distanceSquared > 16){
							continue;
						}

						$modifier = 0.25 * (4 - floor(sqrt($distanceSquared)));
						if($modifier <= 0){
							continue;
						}

						$eff = clone $effect;
						if($eff->isInstant()){
							$eff->setPotency($modifier);
						}else{
							$duration = (int) round($effect->getDuration() * 0.75 * $modifier);
							if($duration < 20){
								continue;
							}
							$eff->setDuration($duration);
						}
						$e->addEffect($eff);
					}
				}
			}
		}
	}

	protected function onHit(ProjectileHitEvent $event) : void{
		$this->splash();

		$this->flagForDespawn();
	}

	protected function onHitEntity(Entity $entityHit, RayTraceResult $hitResult) : bool{
		return false;
	}

	public function entityBaseTick($tickDiff = 1){
		if($this->closed){
			return false;
		}

		$hasUpdate = parent::entityBaseTick($tickDiff);

		if($this->age > 1200){
			$this->flagForDespawn();
			$hasUpdate = true;
		}

		return $hasUpdate;
	}
}
