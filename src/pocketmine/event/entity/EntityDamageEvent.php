<?php

declare(strict_types=1);

namespace pocketmine\event\entity;

use pocketmine\entity\Entity;
use pocketmine\event\Cancellable;

use function array_sum;
use function is_array;
use function max;

/**
 * Called when an entity takes damage.
 */
class EntityDamageEvent extends EntityEvent implements Cancellable{
	public static $handlerList = null;

	public const MODIFIER_BASE = 0;
	public const MODIFIER_ARMOR = 1;
	public const MODIFIER_STRENGTH = 2;
	public const MODIFIER_WEAKNESS = 3;
	public const MODIFIER_RESISTANCE = 4;
	public const MODIFIER_CRITICAL = 5;
	public const MODIFIER_ENCHANTMENT_PROTECTION = 6;
	public const MODIFIER_ENCHANTMENT_SHARPNESS = 7;
	public const MODIFIER_ENCHANTMENT_POWER = 8;
	public const MODIFIER_ABSORPTION = 9;
	public const MODIFIER_TOTEM = 10;
	public const MODIFIER_ENCHANTMENT_FEATHER_FALLING = 11;
	public const MODIFIER_PREVIOUS_DAMAGE_COOLDOWN = 12;

	public const CAUSE_CONTACT = 0;
	public const CAUSE_ENTITY_ATTACK = 1;
	public const CAUSE_PROJECTILE = 2;
	public const CAUSE_SUFFOCATION = 3;
	public const CAUSE_FALL = 4;
	public const CAUSE_FIRE = 5;
	public const CAUSE_FIRE_TICK = 6;
	public const CAUSE_LAVA = 7;
	public const CAUSE_DROWNING = 8;
	public const CAUSE_BLOCK_EXPLOSION = 9;
	public const CAUSE_ENTITY_EXPLOSION = 10;
	public const CAUSE_VOID = 11;
	public const CAUSE_SUICIDE = 12;
	public const CAUSE_MAGIC = 13;
	public const CAUSE_CUSTOM = 14;
	public const CAUSE_STARVATION = 15;


	private $cause;
	/** @var array */
	private $modifiers;
	private $originals;

    /** @var int */
    private $attackCooldown = 10;

	/**
	 * @param Entity    $entity
	 * @param int       $cause
	 * @param int|int[] $damage
	 */
	public function __construct(Entity $entity, int $cause, $damage){
		$this->entity = $entity;
		$this->cause = $cause;
		if(is_array($damage)){
			$this->modifiers = $damage;
		}else{
			$this->modifiers = [
				self::MODIFIER_BASE => $damage
			];
		}

		$this->originals = $this->modifiers;

		if(!isset($this->modifiers[self::MODIFIER_BASE])){
			throw new \InvalidArgumentException("BASE Damage modifier missing");
		}
	}

	/**
	 * @return int
	 */
	public function getCause(){
		return $this->cause;
	}

	/**
	 * @param int $type
	 *
	 * @return int
	 */
	public function getOriginalDamage($type = self::MODIFIER_BASE){
		if(isset($this->originals[$type])){
			return $this->originals[$type];
		}

		return 0;
	}

	/**
	 * @param int $type
	 *
	 * @return int
	 */
	public function getDamage($type = self::MODIFIER_BASE){
		if(isset($this->modifiers[$type])){
			return $this->modifiers[$type];
		}

		return 0;
	}

	/**
	 * @param float $damage
	 * @param int   $type
	 *
	 * @throws \UnexpectedValueException
	 */
	public function setDamage($damage, $type = self::MODIFIER_BASE){
		$this->modifiers[$type] = $damage;
	}

	/**
	 * @param int $type
	 *
	 * @return bool
	 */
	public function isApplicable($type){
		return isset($this->modifiers[$type]);
	}

	/**
	 * @return float
	 */
	public function getFinalDamage(){
		return max(0, array_sum($this->modifiers));
	}


	/**
	 * Returns whether an entity can use armour points to reduce this type of damage.
	 * @return bool
	 */
	public function canBeReducedByArmor() : bool{
		switch($this->cause){
			case self::CAUSE_FIRE_TICK:
			case self::CAUSE_SUFFOCATION:
			case self::CAUSE_DROWNING:
			case self::CAUSE_STARVATION:
			case self::CAUSE_FALL:
			case self::CAUSE_VOID:
			case self::CAUSE_MAGIC:
			case self::CAUSE_SUICIDE:
				return false;
		}

		return true;
	}

    /**
     * Returns the cooldown in ticks before the target entity can be attacked again.
     */
    public function getAttackCooldown() : int{
        return $this->attackCooldown;
    }

    /**
     * Sets the cooldown in ticks before the target entity can be attacked again.
     */
    public function setAttackCooldown(int $attackCooldown) : void{
        $this->attackCooldown = $attackCooldown;
    }
}