<?php

declare(strict_types=1);

namespace pocketmine\item;

use pocketmine\entity\Effect;
use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\event\entity\EntityEatItemEvent;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use function assert;

class Potion extends Item implements FoodSource{

	public const WATER_BOTTLE = 0;
	public const MUNDANE = 1;
	public const MUNDANE_EXTENDED = 2;
	public const THICK = 3;
	public const AWKWARD = 4;
	public const NIGHT_VISION = 5;
	public const NIGHT_VISION_T = 6;
	public const INVISIBILITY = 7;
	public const INVISIBILITY_T = 8;
	public const LEAPING = 9;
	public const LEAPING_T = 10;
	public const LEAPING_TWO = 11;
	public const FIRE_RESISTANCE = 12;
	public const FIRE_RESISTANCE_T = 13;
	public const SPEED = 14;
	public const SPEED_T = 15;
	public const SPEED_TWO = 16;
	public const SLOWNESS = 17;
	public const SLOWNESS_T = 18;
	public const WATER_BREATHING = 19;
	public const WATER_BREATHING_T = 20;
	public const HEALING = 21;
	public const HEALING_TWO = 22;
	public const HARMING = 23;
	public const HARMING_TWO = 24;
	public const POISON = 25;
	public const POISON_T = 26;
	public const POISON_TWO = 27;
	public const REGENERATION = 28;
	public const REGENERATION_T = 29;
	public const REGENERATION_TWO = 30;
	public const STRENGTH = 31;
	public const STRENGTH_T = 32;
	public const STRENGTH_TWO = 33;
	public const WEAKNESS = 34;
	public const WEAKNESS_T = 35;

	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::POTION, $meta, $count, self::getNameByMeta($meta));
	}

	public function canBeConsumed() : bool{
		return true;
	}

	/**
	 * @return int
	 */
	public function getMaxStackSize() : int{
		return 1;
	}

	public function getFoodRestore() : int{
	    return 0;
    }

    public function getSaturationRestore() : float{
	    return 0;
    }

    public function getResidue(){
        return Item::get(Item::GLASS_BOTTLE, 0, 1);
    }

    public function onConsume(Entity $human){
		assert($human instanceof Human);
		$human->level->broadcastLevelSoundEvent($human->add(0, $human->getEyeHeight(), 0), LevelSoundEventPacket::SOUND_BURP);

        $ev = new EntityEatItemEvent($human, $this);
        $ev->call();

        foreach($ev->getAdditionalEffects() as $effect){
            $human->addEffect($effect);
        }

        $human->getInventory()->setItemInHand($ev->getResidue());
	}

	public function getAdditionalEffects() : array{
        return [self::getEffectByMeta($this->meta)];
    }

	public static function getNameByMeta(int $meta) : string{
		switch($meta){
			case self::WATER_BOTTLE:
				return "Water Bottle";
			case self::MUNDANE:
			case self::MUNDANE_EXTENDED:
				return "Mundane Potion";
			case self::THICK:
				return "Thick Potion";
			case self::AWKWARD:
				return "Awkward Potion";
			case self::INVISIBILITY:
			case self::INVISIBILITY_T:
				return "Potion of Invisibility";
			case self::LEAPING:
			case self::LEAPING_T:
				return "Potion of Leaping";
			case self::LEAPING_TWO:
				return "Potion of Leaping II";
			case self::FIRE_RESISTANCE:
			case self::FIRE_RESISTANCE_T:
				return "Potion of Fire Residence";
			case self::SPEED:
			case self::SPEED_T:
				return "Potion of Speed";
			case self::SPEED_TWO:
				return "Potion of Speed II";
			case self::SLOWNESS:
			case self::SLOWNESS_T:
				return "Potion of Slowness";
			case self::WATER_BREATHING:
			case self::WATER_BREATHING_T:
				return "Potion of Water Breathing";
			case self::HARMING:
				return "Potion of Harming";
			case self::HARMING_TWO:
				return "Potion of Harming II";
			case self::POISON:
			case self::POISON_T:
				return "Potion of Poison";
			case self::POISON_TWO:
				return "Potion of Poison II";
			case self::HEALING:
				return "Potion of Healing";
			case self::HEALING_TWO:
				return "Potion of Healing II";
			default:
				return "Potion";
		}
	}

	public static function getEffectByMeta(int $meta) : ?Effect{
		$effect = null;
		switch($meta){
			case Potion::NIGHT_VISION:
				$effect = Effect::getEffect(Effect::NIGHT_VISION)->setAmplifier(0)->setDuration(3 * 60 * 20);
				break;
			case Potion::NIGHT_VISION_T:
				$effect = Effect::getEffect(Effect::NIGHT_VISION)->setAmplifier(0)->setDuration(8 * 60 * 20);
				break;
			case Potion::INVISIBILITY:
				$effect = Effect::getEffect(Effect::INVISIBILITY)->setAmplifier(0)->setDuration(3 * 60 * 20);
				break;
			case Potion::INVISIBILITY_T:
				$effect = Effect::getEffect(Effect::INVISIBILITY)->setAmplifier(0)->setDuration(8 * 60 * 20);
				break;
			case Potion::LEAPING:
				$effect = Effect::getEffect(Effect::JUMP)->setAmplifier(0)->setDuration(3 * 60 * 20);
				break;
			case Potion::LEAPING_T:
				$effect = Effect::getEffect(Effect::JUMP)->setAmplifier(0)->setDuration(8 * 60 * 20);
				break;
			case Potion::LEAPING_TWO:
				$effect = Effect::getEffect(Effect::JUMP)->setAmplifier(1)->setDuration((int) (1.5 * 60 * 20));
				break;
			case Potion::FIRE_RESISTANCE:
				$effect = Effect::getEffect(Effect::FIRE_RESISTANCE)->setAmplifier(0)->setDuration(3 * 60 * 20);
				break;
			case Potion::FIRE_RESISTANCE_T:
				$effect = Effect::getEffect(Effect::FIRE_RESISTANCE)->setAmplifier(0)->setDuration(8 * 60 * 20);
				break;
			case Potion::SPEED:
				$effect = Effect::getEffect(Effect::SPEED)->setAmplifier(0)->setDuration(3 * 60 * 20);
				break;
			case Potion::SPEED_T:
				$effect = Effect::getEffect(Effect::SPEED)->setAmplifier(0)->setDuration(8 * 60 * 20);
				break;
			case Potion::SPEED_TWO:
				$effect = Effect::getEffect(Effect::SPEED)->setAmplifier(1)->setDuration((int) (1.5 * 60 * 20));
				break;
			case Potion::SLOWNESS:
				$effect = Effect::getEffect(Effect::SLOWNESS)->setAmplifier(0)->setDuration(1 * 60 * 20);
				break;
			case Potion::SLOWNESS_T:
				$effect = Effect::getEffect(Effect::SLOWNESS)->setAmplifier(0)->setDuration(4 * 60 * 20);
				break;
			case Potion::WATER_BREATHING:
				$effect = Effect::getEffect(Effect::WATER_BREATHING)->setAmplifier(0)->setDuration(3 * 60 * 20);
				break;
			case Potion::WATER_BREATHING_T:
				$effect = Effect::getEffect(Effect::WATER_BREATHING)->setAmplifier(0)->setDuration(8 * 60 * 20);
				break;
			case Potion::POISON:
				$effect = Effect::getEffect(Effect::POISON)->setAmplifier(0)->setDuration(45 * 20);
				break;
			case Potion::POISON_T:
				$effect = Effect::getEffect(Effect::POISON)->setAmplifier(0)->setDuration(2 * 60 * 20);
				break;
			case Potion::POISON_TWO:
				$effect = Effect::getEffect(Effect::POISON)->setAmplifier(0)->setDuration(22 * 20);
				break;
			case Potion::REGENERATION:
				$effect = Effect::getEffect(Effect::REGENERATION)->setAmplifier(0)->setDuration(45 * 20);
				break;
			case Potion::REGENERATION_T:
				$effect = Effect::getEffect(Effect::REGENERATION)->setAmplifier(0)->setDuration(2 * 60 * 20);
				break;
			case Potion::REGENERATION_TWO:
				$effect = Effect::getEffect(Effect::REGENERATION)->setAmplifier(1)->setDuration(22 * 20);
				break;
			case Potion::STRENGTH:
				$effect = Effect::getEffect(Effect::STRENGTH)->setAmplifier(0)->setDuration(3 * 60 * 20);
				break;
			case Potion::STRENGTH_T:
				$effect = Effect::getEffect(Effect::STRENGTH)->setAmplifier(0)->setDuration(8 * 60 * 20);
				break;
			case Potion::STRENGTH_TWO:
				$effect = Effect::getEffect(Effect::STRENGTH)->setAmplifier(1)->setDuration((int) (1.5 * 60 * 20));
				break;
			case Potion::WEAKNESS:
				$effect = Effect::getEffect(Effect::WEAKNESS)->setAmplifier(0)->setDuration((int) (1.5 * 60 * 20));
				break;
			case Potion::WEAKNESS_T:
				$effect = Effect::getEffect(Effect::WEAKNESS)->setAmplifier(0)->setDuration(4 * 60 * 20);
				break;
			case Potion::HEALING:
				$effect = Effect::getEffect(Effect::HEALING)->setAmplifier(0)->setDuration(1);
				break;
			case Potion::HEALING_TWO:
				$effect = Effect::getEffect(Effect::HEALING)->setAmplifier(1)->setDuration(1);
				break;
			case Potion::HARMING:
				$effect = Effect::getEffect(Effect::HARMING)->setAmplifier(0)->setDuration(1);
				break;
			case Potion::HARMING_TWO:
				$effect = Effect::getEffect(Effect::HARMING)->setAmplifier(1)->setDuration(1);
				break;
		}
		return $effect;
	}

}