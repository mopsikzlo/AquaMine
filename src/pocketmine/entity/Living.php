<?php

declare(strict_types=1);

namespace pocketmine\entity;

use pocketmine\BedrockPlayer;
use pocketmine\block\Block;
use pocketmine\event\entity\EntityDamageByChildEntityEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\event\Timings;
use pocketmine\item\Item as ItemItem;
use pocketmine\math\Vector3;
use pocketmine\math\VoxelRayTrace;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\network\bedrock\protocol\MobEffectPacket as BedrockMobEffectPacket;
use pocketmine\network\mcpe\protocol\EntityEventPacket;
use pocketmine\network\mcpe\protocol\MobEffectPacket;
use pocketmine\Player;
use pocketmine\utils\Binary;
use UnexpectedValueException;
use function abs;
use function array_shift;
use function count;
use function floor;
use function sqrt;

abstract class Living extends Entity implements Damageable{

	public $gravity = 0.08;
	public $drag = 0.02;

	protected $attackTime = 0;

	protected $invisible = false;

	protected $jumpVelocity = 0.42;

	/** @var Effect[] */
	protected $effects = [];

	abstract public function getName();

	protected function initEntity(){
		parent::initEntity();

		if($this->namedtag->hasTag("HealF", FloatTag::class)){
			$this->namedtag->setFloat("Health", $this->namedtag->getFloat("HealF"));
			$this->namedtag->removeTag("HealF");
		}elseif($this->namedtag->hasTag("Health")){
			$tag = $this->namedtag->getTag("Health");
			if(!($tag instanceof FloatTag)){
				$this->namedtag->setFloat("Health", (float) $tag->getValue());
			}
		}else{
			$this->namedtag->setFloat("Health", (float) $this->getMaxHealth());
		}

		$this->setHealth($this->namedtag->getFloat("Health"));

		if($this->namedtag->hasTag("ActiveEffects", ListTag::class)){
			foreach($this->namedtag->getListTag("ActiveEffects") as $e){
				if(!$e instanceof CompoundTag){
					throw new UnexpectedValueException("Bad effect tag type, expected TAG_Compound, got " . $e->getType());
				}

				$amplifier = Binary::unsignByte($e->getByte("Amplifier")); //0-255 only

				$effect = Effect::getEffect($e->getByte("Id"));
				if($effect === null){
					continue;
				}

				$effect->setAmplifier($amplifier)->setDuration($e->getInt("Duration"))->setVisible($e->getByte("ShowParticles") > 0);

				$this->addEffect($effect);
			}
		}
	}

	protected function addAttributes(){
		$this->attributeMap->addAttribute(Attribute::getAttribute(Attribute::HEALTH));
		$this->attributeMap->addAttribute(Attribute::getAttribute(Attribute::FOLLOW_RANGE));
		$this->attributeMap->addAttribute(Attribute::getAttribute(Attribute::KNOCKBACK_RESISTANCE));
		$this->attributeMap->addAttribute(Attribute::getAttribute(Attribute::MOVEMENT_SPEED));
		$this->attributeMap->addAttribute(Attribute::getAttribute(Attribute::ATTACK_DAMAGE));
		$this->attributeMap->addAttribute(Attribute::getAttribute(Attribute::ABSORPTION));
	}

	public function setHealth($amount){
		$wasAlive = $this->isAlive();
		parent::setHealth($amount);
		$this->attributeMap->getAttribute(Attribute::HEALTH)->setValue($this->getHealth(), true);
		if($this->isAlive() and !$wasAlive){
			$pk = new EntityEventPacket();
			$pk->entityRuntimeId = $this->getId();
			$pk->event = EntityEventPacket::RESPAWN;
			$this->server->broadcastPacket($this->hasSpawned, $pk);
		}
	}

	public function getMaxHealth(){
		return $this->attributeMap->getAttribute(Attribute::HEALTH)->getMaxValue();
	}

	public function setMaxHealth($amount){
		$this->attributeMap->getAttribute(Attribute::HEALTH)->setMaxValue($amount)->setDefaultValue($amount);
	}

	public function getAbsorption() : float{
		return $this->attributeMap->getAttribute(Attribute::ABSORPTION)->getValue();
	}

	public function setAbsorption(float $absorption){
		$this->attributeMap->getAttribute(Attribute::ABSORPTION)->setValue($absorption);
	}

	public function saveNBT(){
		parent::saveNBT();
		$this->namedtag->setFloat("Health", $this->getHealth());

		if(count($this->effects) > 0){
			$effects = [];
			foreach($this->effects as $effect){
				$effects[] = CompoundTag::create()
					->setByte("Id", $effect->getId())
					->setByte("Amplifier", Binary::signByte($effect->getAmplifier()))
					->setInt("Duration", $effect->getDuration())
					->setByte("Ambient", 0)
					->setByte("ShowParticles", $effect->isVisible() ? 1 : 0);
			}

			$this->namedtag->setTag("ActiveEffects", new ListTag($effects));
		}else{
			$this->namedtag->removeTag("ActiveEffects");
		}
	}


	public function hasLineOfSight(Entity $entity){
		//TODO: head height
		return true;
		//return $this->getLevel()->rayTraceBlocks(Vector3::createVector($this->x, $this->y + $this->height, $this->z), Vector3::createVector($entity->x, $entity->y + $entity->height, $entity->z)) === null;
	}

	/**
	 * Returns an array of Effects currently active on the mob.
	 * @return Effect[]
	 */
	public function getEffects() : array{
		return $this->effects;
	}

	/**
	 * Removes all effects from the mob.
	 */
	public function removeAllEffects(){
		foreach($this->effects as $effect){
			$this->removeEffect($effect->getId());
		}
	}

	/**
	 * Removes the effect with the specified ID from the mob.
	 *
	 * @param int $effectId
	 */
	public function removeEffect(int $effectId){
		if(isset($this->effects[$effectId])){
			$effect = $this->effects[$effectId];
			unset($this->effects[$effectId]);
			$effect->remove($this);

			$this->recalculateEffectColor();
		}
	}

	/**
	 * Returns the effect instance active on this entity with the specified ID, or null if the mob does not have the
	 * effect.
	 *
	 * @param int $effectId
	 *
	 * @return Effect|null
	 */
	public function getEffect(int $effectId){
		return $this->effects[$effectId] ?? null;
	}

	/**
	 * Returns whether the specified effect is active on the mob.
	 *
	 * @param int $effectId
	 *
	 * @return bool
	 */
	public function hasEffect(int $effectId) : bool{
		return isset($this->effects[$effectId]);
	}

	/**
	 * Adds an effect to the mob.
	 * If a weaker effect of the same type is already applied, it will be replaced.
	 * If a weaker or equal-strength effect is already applied but has a shorter duration, it will be replaced.
	 *
	 * @param Effect $effect
	 */
	public function addEffect(Effect $effect){
		if(isset($this->effects[$effect->getId()])){
			$oldEffect = $this->effects[$effect->getId()];
			if(
				abs($effect->getAmplifier()) < $oldEffect->getAmplifier()
				or (abs($effect->getAmplifier()) === abs($oldEffect->getAmplifier()) and $effect->getDuration() < $oldEffect->getDuration())
			){
				return;
			}
			$effect->add($this, true, $oldEffect);
		}else{
			$effect->add($this, false);
		}

		$this->effects[$effect->getId()] = $effect;

		$this->recalculateEffectColor();
	}

	/**
	 * Recalculates the mob's potion bubbles colour based on the active effects.
	 */
	protected function recalculateEffectColor(){
		//TODO: add transparency values
		$color = [0, 0, 0]; //RGB
		$count = 0;
		$ambient = true;
		foreach($this->effects as $effect){
			if($effect->isVisible() and $effect->hasBubbles()){
				$c = $effect->getColor();
				$color[0] += $c[0] * $effect->getEffectLevel();
				$color[1] += $c[1] * $effect->getEffectLevel();
				$color[2] += $c[2] * $effect->getEffectLevel();
				$count += $effect->getEffectLevel();
				if(!$effect->isAmbient()){
					$ambient = false;
				}
			}
		}

		if($count > 0){
			$r = ($color[0] / $count) & 0xff;
			$g = ($color[1] / $count) & 0xff;
			$b = ($color[2] / $count) & 0xff;

			$this->setDataProperty(Entity::DATA_POTION_COLOR, Entity::DATA_TYPE_INT, 0xff000000 | ($r << 16) | ($g << 8) | $b);
			$this->setDataProperty(Entity::DATA_POTION_AMBIENT, Entity::DATA_TYPE_BYTE, $ambient ? 1 : 0);
		}else{
			$this->setDataProperty(Entity::DATA_POTION_COLOR, Entity::DATA_TYPE_INT, 0);
			$this->setDataProperty(Entity::DATA_POTION_AMBIENT, Entity::DATA_TYPE_BYTE, 0);
		}
	}

	/**
	 * Sends the mob's potion effects to the specified player.
	 * @param Player $player
	 */
	public function sendPotionEffects(Player $player){
		foreach($this->effects as $effect){
			$this->sendEffect($player, $effect);
		}
	}

	/**
	 * @param Player $player
	 * @param Effect $effect
	 */
	public function sendEffect(Player $player, Effect $effect) : void{
		if($player instanceof BedrockPlayer){
			$pk = new BedrockMobEffectPacket();
			$pk->actorRuntimeId = $this->id;
			$pk->effectId = $effect->getId();
			$pk->amplifier = $effect->getAmplifier();
			$pk->particles = $effect->isVisible();
			$pk->duration = $effect->getDuration();
			$pk->eventId = BedrockMobEffectPacket::EVENT_ADD;

			$player->sendDataPacket($pk);
		}else{
			$pk = new MobEffectPacket();
			$pk->entityRuntimeId = $this->id;
			$pk->effectId = $effect->getId();
			$pk->amplifier = $effect->getAmplifier();
			$pk->particles = $effect->isVisible();
			$pk->duration = $effect->getDuration();
			$pk->eventId = MobEffectPacket::EVENT_ADD;

			$player->sendDataPacket($pk);
		}
	}


	/**
	 * Returns the initial upwards velocity of a jumping entity in blocks/tick, including additional velocity due to effects.
	 * @return float
	 */
	public function getJumpVelocity() : float{
		return $this->jumpVelocity + ($this->hasEffect(Effect::JUMP) ? ($this->getEffect(Effect::JUMP)->getEffectLevel() / 10) : 0);
	}

	/**
	 * Called when the entity jumps from the ground. This method adds upwards velocity to the entity.
	 */
	public function jump(){
		if($this->onGround){
			$this->motionY = $this->getJumpVelocity(); //Y motion should already be 0 if we're jumping from the ground.
		}
	}

	public function fall(float $fallDistance){
		if($this->level->getBlockIdAt($this->getFloorX(), $this->getFloorY() - 1, $this->getFloorZ()) === Block::SLIME_BLOCK and !$this->isSneaking()){
			$this->motionY = $this->gravity * $fallDistance;
			$this->resetFallDistance();
			return;
		}
		$damage = floor($fallDistance - 3 - ($this->hasEffect(Effect::JUMP) ? $this->getEffect(Effect::JUMP)->getEffectLevel() : 0));
		if($damage > 0){
			$ev = new EntityDamageEvent($this, EntityDamageEvent::CAUSE_FALL, $damage);
			$this->attack($ev->getFinalDamage(), $ev);
		}
	}

	public function applyDamageModifiers(EntityDamageEvent $source) : void{
		if($this->lastDamageCause !== null and $this->attackTime > 0){
			if($this->lastDamageCause->getDamage() >= $source->getDamage()){
				$source->setCancelled();
			}
			$source->setDamage(-$this->lastDamageCause->getDamage(), EntityDamageEvent::MODIFIER_PREVIOUS_DAMAGE_COOLDOWN);
		}
		if($this->hasEffect(Effect::DAMAGE_RESISTANCE)){
			$source->setDamage(-($source->getDamage(EntityDamageEvent::MODIFIER_BASE) * 0.20 * $this->getEffect(Effect::DAMAGE_RESISTANCE)->getEffectLevel()), EntityDamageEvent::MODIFIER_RESISTANCE);
		}

		parent::applyDamageModifiers($source);
	}

	public function attack($damage, EntityDamageEvent $source){
		if($this->noDamageTicks > 0){
			$source->setCancelled();
		}

		if($this->hasEffect(Effect::FIRE_RESISTANCE) and (
				$source->getCause() === EntityDamageEvent::CAUSE_FIRE
				or $source->getCause() === EntityDamageEvent::CAUSE_FIRE_TICK
				or $source->getCause() === EntityDamageEvent::CAUSE_LAVA
			)
		){
			$source->setCancelled();
		}

		parent::attack($damage, $source);

		if($source->isCancelled()){
			return;
		}

		if($source instanceof EntityDamageByEntityEvent){
			if($source instanceof EntityDamageByChildEntityEvent){
				$e = $source->getChild();
			}else{
				$e = $source->getDamager();
			}

			if($e !== null){
				if($e->isOnFire() > 0){
					$this->setOnFire(2 * $this->server->getDifficulty());
				}

				if($source instanceof EntityDamageByChildEntityEvent){
					$deltaX = $e->motionX;
					$deltaZ = $e->motionZ;
				}else{
					$deltaX = $this->x - $e->x;
					$deltaZ = $this->z - $e->z;
				}
				$this->knockBack($deltaX, $deltaZ, $source->getKnockBack(), $source->getKnockBackHeight(), $source->getKnockBackHeightCap());

				$source->applyPostAttack();
			}
		}

		$pk = new EntityEventPacket();
		$pk->entityRuntimeId = $this->getId();
		$pk->event = $this->getHealth() <= 0 ? EntityEventPacket::DEATH_ANIMATION : EntityEventPacket::HURT_ANIMATION; //Ouch!
		$this->server->broadcastPacket($this->hasSpawned, $pk);

		$this->attackTime = 10; //0.5 seconds cooldown
	}

	public function knockBack(float $diffX, float $diffZ, float $power, float $height = 0.4, float $heightCap = 0.4){
		$len = sqrt($diffX ** 2 + $diffZ ** 2);
		if($len == 0){
			return;
		}
		$lenInv = 1 / $len;

		$motion = $this->getSpeed();

		$motion->x /= 2;
		$motion->y /= 2;
		$motion->z /= 2;

		$motion->x += $diffX * $lenInv * $power * 0.4;
		$motion->y += $height;
		$motion->z += $diffZ * $lenInv * $power * 0.4;

		if($motion->y > $heightCap){
			$motion->y = $heightCap;
		}

		$this->setMotion($motion);
	}

	public function kill(){
		if(!$this->isAlive()){
			return;
		}
		parent::kill();
		$this->callDeathEvent();
	}

	protected function callDeathEvent(){
		$ev = new EntityDeathEvent($this, $this->getDrops());
		$ev->call();
		foreach($ev->getDrops() as $item){
			$this->getLevel()->dropItem($this, $item);
		}
	}

	public function entityBaseTick($tickDiff = 1){
		Timings::$timerLivingEntityBaseTick->startTiming();
		$this->setDataFlag(self::DATA_FLAGS, self::DATA_FLAG_BREATHING, !$this->isInsideOfWater());

		$hasUpdate = parent::entityBaseTick($tickDiff);

		$this->doEffectsTick($tickDiff);

		if($this->isAlive()){
			if($this->isInsideOfSolid()){
				$hasUpdate = true;
				$ev = new EntityDamageEvent($this, EntityDamageEvent::CAUSE_SUFFOCATION, 1);
				$this->attack($ev->getFinalDamage(), $ev);
			}

			if(!$this->hasEffect(Effect::WATER_BREATHING) and $this->isInsideOfWater()){
				if($this instanceof WaterAnimal){
					$this->setDataProperty(self::DATA_AIR, self::DATA_TYPE_SHORT, 400);
				}else{
					$hasUpdate = true;
					$airTicks = $this->getDataProperty(self::DATA_AIR) - $tickDiff;
					if($airTicks <= -20){
						$airTicks = 0;

						$ev = new EntityDamageEvent($this, EntityDamageEvent::CAUSE_DROWNING, 2);
						$this->attack($ev->getFinalDamage(), $ev);
					}
					$this->setDataProperty(self::DATA_AIR, self::DATA_TYPE_SHORT, $airTicks);
				}
			}else{
				if($this instanceof WaterAnimal){
					$hasUpdate = true;
					$airTicks = $this->getDataProperty(self::DATA_AIR) - $tickDiff;
					if($airTicks <= -20){
						$airTicks = 0;

						$ev = new EntityDamageEvent($this, EntityDamageEvent::CAUSE_SUFFOCATION, 2);
						$this->attack($ev->getFinalDamage(), $ev);
					}
					$this->setDataProperty(self::DATA_AIR, self::DATA_TYPE_SHORT, $airTicks);
				}else{
					$this->setDataProperty(self::DATA_AIR, self::DATA_TYPE_SHORT, 400);
				}
			}
		}

		if($this->attackTime > 0){
			$this->attackTime -= $tickDiff;
		}

		Timings::$timerLivingEntityBaseTick->stopTiming();

		return $hasUpdate;
	}

	protected function doEffectsTick(int $tickDiff = 1){
		if(count($this->effects) > 0){
			foreach($this->effects as $effect){
				if($effect->canTick()){
					$effect->applyEffect($this);
				}
				$duration = $effect->getDuration() - $tickDiff;
				if($duration <= 0){
					$this->removeEffect($effect->getId());
				}else{
					$effect->setDuration($duration);
				}
			}
		}
	}

	protected function dealFireDamage(){
		if(!$this->hasEffect(Effect::FIRE_RESISTANCE)){
			parent::dealFireDamage();
		}
	}

	/**
	 * @return ItemItem[]
	 */
	public function getDrops() : array{
		return [];
	}

	/**
	 * @param int   $maxDistance
	 * @param int   $maxLength
	 * @param array $transparent
	 *
	 * @return Block[]
	 */
	public function getLineOfSight($maxDistance, $maxLength = 0, array $transparent = []){
		if($maxDistance > 120){
			$maxDistance = 120;
		}

		if(count($transparent) === 0){
			$transparent = null;
		}

		$blocks = [];
		$nextIndex = 0;

		foreach(VoxelRayTrace::inDirection($this->add(0, $this->eyeHeight, 0), $this->getDirectionVector(), $maxDistance) as $vector3){
			$block = $this->level->getBlockAt($vector3->x, $vector3->y, $vector3->z);
			$blocks[$nextIndex++] = $block;

			if($maxLength !== 0 and count($blocks) > $maxLength){
				array_shift($blocks);
				--$nextIndex;
			}

			$id = $block->getId();

			if($transparent === null){
				if($id !== 0){
					break;
				}
			}else{
				if(!isset($transparent[$id])){
					break;
				}
			}
		}

		return $blocks;
	}

	/**
	 * @param int   $maxDistance
	 * @param array $transparent
	 *
	 * @return Block|null
	 */
	public function getTargetBlock($maxDistance, array $transparent = []){
		try{
			$block = $this->getLineOfSight($maxDistance, 1, $transparent)[0];
			if($block instanceof Block){
				return $block;
			}
		}catch(\ArrayOutOfBoundsException $e){
		}

		return null;
	}
}
