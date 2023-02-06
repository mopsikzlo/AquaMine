<?php

declare(strict_types=1);

namespace pocketmine\event\entity;

use pocketmine\entity\Effect;
use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\entity\Living;
use pocketmine\inventory\PlayerInventory;
use pocketmine\item\enchantment\Enchantment;

/**
 * Called when an entity takes damage from another entity.
 */
class EntityDamageByEntityEvent extends EntityDamageEvent{

	/** @var Entity */
	protected $damager;
	/** @var float */
	protected $knockBack;
	/** @var float */
	protected $knockBackHeight;
	/** @var float */
	protected $knockBackHeightCap;
	/** @var bool */
	protected $sprintBreaking = true;

	/**
	 * @param Entity $damager
	 * @param Entity $entity
	 * @param int $cause
	 * @param int|int[] $damage
	 * @param float $knockBack
	 */
	public function __construct(Entity $damager, Entity $entity, int $cause, $damage, float $knockBack = 1.0, float $knockBackHeight = 0.4, float $knockBackHeightCap = 0.4, bool $sprintBreaking = true){
		$this->damager = $damager;
		$this->knockBack = $knockBack;
		$this->knockBackHeight = $knockBackHeight;
		$this->knockBackHeightCap = $knockBackHeightCap;
		$this->sprintBreaking = $sprintBreaking;
		parent::__construct($entity, $cause, $damage);
		$this->addAttackerModifiers();
	}

	protected function addAttackerModifiers(){
		if($this->damager instanceof Living){ //TODO: move this to entity classes
			if($this->damager->hasEffect(Effect::STRENGTH)){
				$this->setDamage($this->getDamage(self::MODIFIER_BASE) * 0.3 * $this->damager->getEffect(Effect::STRENGTH)->getEffectLevel(), self::MODIFIER_STRENGTH);
			}

			if($this->damager->hasEffect(Effect::WEAKNESS)){
				$this->setDamage(-($this->getDamage(self::MODIFIER_BASE) * 0.2 * $this->damager->getEffect(Effect::WEAKNESS)->getEffectLevel()), self::MODIFIER_WEAKNESS);
			}

			if($this->getCause() === self::CAUSE_ENTITY_ATTACK and $this->damager instanceof Human){
				$inventory = $this->damager->getInventory();

				if($inventory instanceof PlayerInventory){
					$item = $inventory->getItemInHand();

					if(($enchantment = $item->getEnchantment(Enchantment::SHARPNESS)) !== null){
						$this->setDamage($enchantment->getLevel() * 1.25, self::MODIFIER_ENCHANTMENT_SHARPNESS);
					}
				}
			}
		}
	}

	public function applyPostAttack() : void{
		if($this->getCause() === self::CAUSE_ENTITY_ATTACK and $this->damager instanceof Human){
			$inventory = $this->damager->getInventory();

			if($inventory instanceof PlayerInventory){
				$item = $inventory->getItemInHand();

				if($this->entity instanceof Living and ($enchantment = $item->getEnchantment(Enchantment::KNOCKBACK)) !== null){
					$this->entity->knockBack($this->entity->x - $this->damager->x, $this->entity->z - $this->damager->z, $enchantment->getLevel() * 1.25);
				}

				if(($enchantment = $item->getEnchantment(Enchantment::FIRE_ASPECT)) !== null){
					$this->entity->setOnFire(4.0 * $enchantment->getLevel());
				}
			}
		}
	}

	/**
	 * Returns the attacking entity, or null if the attacker has been killed or closed.
	 *
	 * @return Entity|null
	 */
	public function getDamager() : ?Entity{
		if($this->damager !== null and ($this->damager->isFlaggedForDespawn() or $this->damager->closed)){
			$this->damager = null;
		}
		return $this->damager;
	}

	/**
	 * @return float
	 */
	public function getKnockBack() : float{
		return $this->knockBack;
	}

	/**
	 * @param float $knockBack
	 */
	public function setKnockBack(float $knockBack) : void{
		$this->knockBack = $knockBack;
	}

	/**
	 * @return float
	 */
	public function getKnockBackHeight() : float{
		return $this->knockBackHeight;
	}

	/**
	 * @param float $knockBackHeight
	 */
	public function setKnockBackHeight(float $knockBackHeight) : void{
		$this->knockBackHeight = $knockBackHeight;
	}

	/**
	 * @return float
	 */
	public function getKnockBackHeightCap() : float{
		return $this->knockBackHeightCap;
	}

	/**
	 * @param float $knockBackHeightCap
	 */
	public function setKnockBackHeightCap(float $knockBackHeightCap) : void{
		$this->knockBackHeightCap = $knockBackHeightCap;
	}

	/**
	 * @return bool
	 */
	public function isSprintBreaking() : bool{
		return $this->sprintBreaking;
	}

	/**
	 * @param bool $sprintBreaking
	 */
	public function setSprintBreaking(bool $sprintBreaking) : void{
		$this->sprintBreaking = $sprintBreaking;
	}
}
