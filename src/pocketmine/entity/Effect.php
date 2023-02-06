<?php

declare(strict_types=1);

namespace pocketmine\entity;

use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityEffectAddEvent;
use pocketmine\event\entity\EntityEffectRemoveEvent;
use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\network\mcpe\protocol\MobEffectPacket;
use pocketmine\Player;
use pocketmine\utils\Config;

use function hexdec;

use const INT32_MAX;

class Effect{
	public const SPEED = 1;
	public const SLOWNESS = 2;
	public const HASTE = 3;
	public const FATIGUE = 4, MINING_FATIGUE = 4;
	public const STRENGTH = 5;
	public const INSTANT_HEALTH = 6, HEALING = 6;
	public const INSTANT_DAMAGE = 7, HARMING = 7;
	public const JUMP = 8;
	public const NAUSEA = 9, CONFUSION = 9;
	public const REGENERATION = 10;
	public const DAMAGE_RESISTANCE = 11;
	public const FIRE_RESISTANCE = 12;
	public const WATER_BREATHING = 13;
	public const INVISIBILITY = 14;
	public const BLINDNESS = 15;
	public const NIGHT_VISION = 16;
	public const HUNGER = 17;
	public const WEAKNESS = 18;
	public const POISON = 19;
	public const WITHER = 20;
	public const HEALTH_BOOST = 21;
	public const ABSORPTION = 22;
	public const SATURATION = 23;
	public const LEVITATION = 24; //TODO

	/** @var Effect[] */
	protected static $effects = [];

	public static function init(){
		$config = new Config(\pocketmine\PATH . "src/pocketmine/resources/effects.json", Config::JSON, []);

		foreach($config->getAll() as $name => $data){
			$color = hexdec($data["color"]);
			$r = ($color >> 16) & 0xff;
			$g = ($color >> 8) & 0xff;
			$b = $color & 0xff;
			self::registerEffect($name, new Effect(
				$data["id"],
				"%" . $data["name"],
				$r,
				$g,
				$b,
				$data["isBad"] ?? false,
				$data["default_duration"] ?? 300 * 20,
				$data["has_bubbles"] ?? true
			));
		}
	}

	public static function registerEffect(string $internalName, Effect $effect){
		self::$effects[$effect->getId()] = $effect;
		self::$effects[$internalName] = $effect;
	}

	/**
	 * @param int $id
	 *
	 * @return Effect|null
	 */
	public static function getEffect($id){
		if(isset(self::$effects[$id])){
			return clone self::$effects[(int) $id];
		}
		return null;
	}

	/**
	 * Alias for Effect::getEffect(id)
	 * @see Effect::getEffect()
	 *
	 * @param int $id
	 *
	 * @return Effeect|null
	 */
	public static function get(int $id) : ?Effect{
		return self::getEffect($id);
	}

	/**
	 * @param string $name
	 *
	 * @return Effect|null
	 */
	public static function getEffectByName($name){
		if(isset(self::$effects[$name])){
			return clone self::$effects[$name];
		}
		return null;
	}

	/** @var int */
	protected $id;

	protected $name;

	protected $duration;

	protected $amplifier = 0;

	protected $color;

	protected $show = true;

	protected $ambient = false;

	protected $bad;

	protected $defaultDuration = 300 * 20;

	protected $hasBubbles = true;
	/** @var float */
	protected $potency = 1;

	/**
	 * @param int    $id              Effect ID as per Minecraft PE
	 * @param string $name            Translation key used for effect name
	 * @param int    $r               0-255, red balance of potion particle colour
	 * @param int    $g               0-255, green balance of potion particle colour
	 * @param int    $b               0-255, blue balance of potion particle colour
	 * @param bool   $isBad           Whether the effect is harmful
	 * @param int    $defaultDuration Duration in ticks the effect will last for by default if applied without a duration.
	 * @param bool   $hasBubbles      Whether the effect has potion bubbles. Some do not (e.g. Instant Damage has its own particles instead of bubbles)
	 */
	public function __construct($id, $name, $r, $g, $b, $isBad = false, int $defaultDuration = 300 * 20, bool $hasBubbles = true){
		$this->id = $id;
		$this->name = $name;
		$this->bad = (bool) $isBad;
		$this->setColor($r, $g, $b);
		$this->defaultDuration = $defaultDuration;
		$this->duration = $defaultDuration;
		$this->hasBubbles = $hasBubbles;
	}

	/**
	 * Returns the translation key used to translate this effect's name.
	 * @return string
	 */
	public function getName(){
		return $this->name;
	}

	/**
	 * Returns the effect ID as per Minecraft PE
	 * @return int
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Sets the duration in ticks of the effect.
	 * @param $ticks
	 *
	 * @return $this
	 */
	public function setDuration(int $ticks){
		if($ticks < 0 or $ticks > INT32_MAX){
			throw new \InvalidArgumentException("Effect duration must be in range of 0 - " . INT32_MAX);
		}
		$this->duration = $ticks;
		return $this;
	}

	/**
	 * Returns the duration remaining of the effect in ticks.
	 * @return int
	 */
	public function getDuration(){
		return $this->duration;
	}

	/**
	 * Returns the default duration this effect will apply for if a duration is not specified.
	 * @return int
	 */
	public function getDefaultDuration() : int{
		return $this->defaultDuration;
	}

	/**
	 * Returns whether this effect will give the subject potion bubbles.
	 * @return bool
	 */
	public function hasBubbles() : bool{
		return $this->hasBubbles;
	}

	/**
	 * Returns whether this effect will produce some visible effect, such as bubbles or particles.
	 * NOTE: Do not confuse this with {@link Effect#hasBubbles}. For example, Instant Damage does not have bubbles, but still produces visible effects (particles).
	 *
	 * @return bool
	 */
	public function isVisible(){
		return $this->show;
	}

	/**
	 * Changes the visibility of the effect.
	 * @param bool $bool
	 *
	 * @return $this
	 */
	public function setVisible($bool){
		$this->show = (bool) $bool;
		return $this;
	}

	/**
	 * Returns the level of this effect, which is always one higher than the amplifier.
	 *
	 * @return int
	 */
	public function getEffectLevel() : int{
		return $this->amplifier + 1;
	}

	/**
	 * Returns the amplifier of this effect.
	 *
	 * @return int
	 */
	public function getAmplifier(){
		return $this->amplifier;
	}

	/**
	 * @param int $amplifier
	 *
	 * @return $this
	 */
	public function setAmplifier(int $amplifier){
		$this->amplifier = ($amplifier & 0xff);
		return $this;
	}

	/**
	 * Returns whether the effect is ambient.
	 * @return bool
	 */
	public function isAmbient(){
		return $this->ambient;
	}

	/**
	 * Sets the ambiency of this effect.
	 * @param bool $ambient
	 *
	 * @return $this
	 */
	public function setAmbient($ambient = true){
		$this->ambient = (bool) $ambient;
		return $this;
	}

	/**
	 * Returns whether this effect is harmful.
	 * TODO: implement inverse effect results for undead mobs
	 *
	 * @return bool
	 */
	public function isBad(){
		return $this->bad;
	}
	
	
	/**
	 * Returns whether this effect is instant.
	 *
	 * @return bool
	 */
	public function isInstant() : bool{
		return $this->id === Effect::INSTANT_DAMAGE or $this->id === Effect::INSTANT_HEALTH or $this->id === Effect::SATURATION;
	}
	
	/**
	 * Returns the potency of the effect.
	 *
	 * @return float
	 */
	public function getPotency() : float{
		return $this->potency;
	}
	
	/**
	 * Sets the potency for instant effect.
	 *
	 * @param float $potency
	 */
	 public function setPotency(float $potency = 1){
	 	if(!$this->isInstant()){
	 		throw new \InvalidStateException("Trying to set potency for non-instant effect!");
	 	}
	 	$this->potency = $potency;
	 }

	/**
	 * Returns whether the effect will do something on the current tick.
	 *
	 * @return bool
	 */
	public function canTick(){
		switch($this->id){
			case Effect::POISON:
				if(($interval = (25 >> $this->amplifier)) > 0){
					return ($this->duration % $interval) === 0;
				}
				return true;
			case Effect::WITHER:
				if(($interval = (50 >> $this->amplifier)) > 0){
					return ($this->duration % $interval) === 0;
				}
				return true;
			case Effect::REGENERATION:
				if(($interval = (40 >> $this->amplifier)) > 0){
					return ($this->duration % $interval) === 0;
				}
				return true;
			case Effect::HUNGER:
				if($this->amplifier < 0){ // prevents hacking with amplifier -1
					return false;
				}
				if(($interval = 20) > 0){
					return ($this->duration % $interval) === 0;
				}
				return true;
			case Effect::INSTANT_DAMAGE:
			case Effect::INSTANT_HEALTH:
			case Effect::SATURATION:
				//If forced to last longer than 1 tick, these apply every tick.
				return true;
		}
		return false;
	}

	/**
	 * Applies effect results to an entity.
	 *
	 * @param Entity $entity
	 */
	public function applyEffect(Entity $entity){
		switch($this->id){
			case Effect::POISON:
				if($entity->getHealth() > 1 and !($entity instanceof Player and $entity->isCreative())){
					$ev = new EntityDamageEvent($entity, EntityDamageEvent::CAUSE_MAGIC, 1);
					$entity->attack($ev->getFinalDamage(), $ev);
				}
				break;

			case Effect::WITHER:
				if(!($entity instanceof Player and $entity->isCreative())){
					$ev = new EntityDamageEvent($entity, EntityDamageEvent::CAUSE_MAGIC, 1);
					$entity->attack($ev->getFinalDamage(), $ev);
				}
				break;

			case Effect::REGENERATION:
				if($entity->getHealth() < $entity->getMaxHealth() and !($entity instanceof Player and $entity->isCreative())){
					$ev = new EntityRegainHealthEvent($entity, 1, EntityRegainHealthEvent::CAUSE_MAGIC);
					$entity->heal($ev->getAmount(), $ev);
				}
				break;

			case Effect::HUNGER:
				if($entity instanceof Human){
					$entity->exhaust(0.5 * $this->getEffectLevel(), PlayerExhaustEvent::CAUSE_POTION);
				}
				break;
			case Effect::INSTANT_HEALTH:
				//TODO: add particles (witch spell)
				$amount = (4 << $this->amplifier) * $this->potency;
				if($amount > 0 and $entity->getHealth() < $entity->getMaxHealth() and !($entity instanceof Player and $entity->isCreative())){
					$entity->heal($amount, new EntityRegainHealthEvent($entity, $amount, EntityRegainHealthEvent::CAUSE_MAGIC));
				}
				break;
			case Effect::INSTANT_DAMAGE:
				//TODO: add particles (witch spell)
				$amount = (4 << $this->amplifier) * $this->potency;
				if($amount > 0 and !($entity instanceof Player and $entity->isCreative())){
					$entity->attack($amount, new EntityDamageEvent($entity, EntityDamageEvent::CAUSE_MAGIC, $amount));
				}
				break;
			case Effect::SATURATION:
				$food = $this->getEffectLevel() * $this->potency;
				if($food > 0 and $entity instanceof Human){
					$entity->addFood($food);
					$entity->addSaturation($food);
				}
				break;
		}
	}

	/**
	 * Returns an RGB color array of this effect's color.
	 * @return array
	 */
	public function getColor(){
		return [$this->color >> 16, ($this->color >> 8) & 0xff, $this->color & 0xff];
	}

	/**
	 * Sets the color of this effect.
	 *
	 * @param int $r
	 * @param int $g
	 * @param int $b
	 */
	public function setColor($r, $g, $b){
		$this->color = (($r & 0xff) << 16) + (($g & 0xff) << 8) + ($b & 0xff);
	}

	/**
	 * Adds this effect to the Entity, performing effect overriding as specified.
	 *
	 * @param Entity      $entity
	 * @param bool        $modify
	 * @param Effect|null $oldEffect
	 */
	public function add(Entity $entity, $modify = false, Effect $oldEffect = null){
		$ev = new EntityEffectAddEvent($entity, $this, $modify, $oldEffect);
		$ev->call();
		if($ev->isCancelled()){
			return;
		}
		if($entity instanceof Player){
			$pk = new MobEffectPacket();
			$pk->entityRuntimeId = $entity->getId();
			$pk->effectId = $this->getId();
			$pk->amplifier = $this->getAmplifier();
			$pk->particles = $this->isVisible();
			$pk->duration = $this->getDuration();
			if($ev->willModify()){
				$pk->eventId = MobEffectPacket::EVENT_MODIFY;
			}else{
				$pk->eventId = MobEffectPacket::EVENT_ADD;
			}

			$entity->sendDataPacket($pk);
		}

		switch($this->id){
			case Effect::INVISIBILITY:
				$entity->setDataFlag(Entity::DATA_FLAGS, Entity::DATA_FLAG_INVISIBLE, true);
				$entity->setNameTagVisible(false);
				break;
			case Effect::SPEED:
				$attr = $entity->getAttributeMap()->getAttribute(Attribute::MOVEMENT_SPEED);
				if($ev->willModify() and $oldEffect !== null){
					$speed = $attr->getValue() / (1 + 0.2 * $oldEffect->getEffectLevel());
				}else{
					$speed = $attr->getValue();
				}
				$speed *= (1 + 0.2 * $this->getEffectLevel());
				$attr->setValue($speed);
				break;
			case Effect::SLOWNESS:
				$attr = $entity->getAttributeMap()->getAttribute(Attribute::MOVEMENT_SPEED);
				if($ev->willModify() and $oldEffect !== null){
					$speed = $attr->getValue() / (1 - 0.15 * $oldEffect->getEffectLevel());
				}else{
					$speed = $attr->getValue();
				}
				$speed *= (1 - 0.15 * $this->getEffectLevel());
				$attr->setValue($speed, true);
				break;

			case Effect::HEALTH_BOOST:
				$attr = $entity->getAttributeMap()->getAttribute(Attribute::HEALTH);
				if($ev->willModify() and $oldEffect !== null){
					$max = $attr->getMaxValue() - (4 * $oldEffect->getEffectLevel());
				}else{
					$max = $attr->getMaxValue();
				}

				$max += (4 * $this->getEffectLevel());
				$attr->setMaxValue($max);
				break;
			case Effect::ABSORPTION:
				$new = (4 * $this->getEffectLevel());
				if($new > $entity->getAbsorption()){
					$entity->setAbsorption($new);
				}
				break;
		}
	}

	/**
	 * Removes the effect from the entity, resetting any changed values back to their original defaults.
	 *
	 * @param Entity $entity
	 */
	public function remove(Entity $entity){
		$ev = new EntityEffectRemoveEvent($entity, $this);
		$ev->call();
		if($ev->isCancelled()){
			return;
		}
		if($entity instanceof Player){
			$pk = new MobEffectPacket();
			$pk->entityRuntimeId = $entity->getId();
			$pk->eventId = MobEffectPacket::EVENT_REMOVE;
			$pk->effectId = $this->getId();

			$entity->sendDataPacket($pk);
		}

		switch($this->id){
			case Effect::INVISIBILITY:
				$entity->setDataFlag(Entity::DATA_FLAGS, Entity::DATA_FLAG_INVISIBLE, false);
				$entity->setNameTagVisible(true);
				break;
			case Effect::SPEED:
				$attr = $entity->getAttributeMap()->getAttribute(Attribute::MOVEMENT_SPEED);
				$attr->setValue($attr->getValue() / (1 + 0.2 * $this->getEffectLevel()));
				break;
			case Effect::SLOWNESS:
				$attr = $entity->getAttributeMap()->getAttribute(Attribute::MOVEMENT_SPEED);
				$attr->setValue($attr->getValue() / (1 - 0.15 * $this->getEffectLevel()));
				break;
			case Effect::HEALTH_BOOST:
				$attr = $entity->getAttributeMap()->getAttribute(Attribute::HEALTH);
				$attr->setMaxValue($attr->getMaxValue() - 4 * $this->getEffectLevel());
				break;
			case Effect::ABSORPTION:
				$entity->setAbsorption(0);
				break;
		}
	}
}
