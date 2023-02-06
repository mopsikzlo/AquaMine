<?php

declare(strict_types=1);

namespace pocketmine\item\enchantment;


use function constant;
use function defined;
use function strtoupper;

class Enchantment{

	public const PROTECTION = 0;
	public const FIRE_PROTECTION = 1;
	public const FEATHER_FALLING = 2;
	public const BLAST_PROTECTION = 3;
	public const PROJECTILE_PROTECTION = 4;
	public const THORNS = 5;
	public const RESPIRATION = 6;
	public const DEPTH_STRIDER = 7;
	public const AQUA_AFFINITY = 8;
	public const SHARPNESS = 9;
	public const SMITE = 10;
	public const BANE_OF_ARTHROPODS = 11;
	public const KNOCKBACK = 12;
	public const FIRE_ASPECT = 13;
	public const LOOTING = 14;
	public const EFFICIENCY = 15;
	public const SILK_TOUCH = 16;
	public const UNBREAKING = 17;
	public const FORTUNE = 18;
	public const POWER = 19;
	public const PUNCH = 20;
	public const FLAME = 21;
	public const INFINITY = 22;
	public const LUCK_OF_THE_SEA = 23;
	public const LURE = 24;
	public const FROST_WALKER = 25;
	public const MENDING = 26;

	public const RARITY_COMMON = 0;
	public const RARITY_UNCOMMON = 1;
	public const RARITY_RARE = 2;
	public const RARITY_MYTHIC = 3;

	public const ACTIVATION_EQUIP = 0;
	public const ACTIVATION_HELD = 1;
	public const ACTIVATION_SELF = 2;

	public const SLOT_NONE = 0;
	public const SLOT_ALL = 0b11111111111111;
	public const SLOT_ARMOR = 0b1111;
	public const SLOT_HEAD = 0b1;
	public const SLOT_TORSO = 0b10;
	public const SLOT_LEGS = 0b100;
	public const SLOT_FEET = 0b1000;
	public const SLOT_SWORD = 0b10000;
	public const SLOT_BOW = 0b100000;
	public const SLOT_TOOL = 0b111000000;
	public const SLOT_HOE = 0b1000000;
	public const SLOT_SHEARS = 0b10000000;
	public const SLOT_FLINT_AND_STEEL = 0b10000000;
	public const SLOT_DIG = 0b111000000000;
	public const SLOT_AXE = 0b1000000000;
	public const SLOT_PICKAXE = 0b10000000000;
	public const SLOT_SHOVEL = 0b10000000000;
	public const SLOT_FISHING_ROD = 0b100000000000;
	public const SLOT_CARROT_STICK = 0b1000000000000;

	/** @var Enchantment[] */
	protected static $enchantments;

	public static function init(){
		self::$enchantments = new \SplFixedArray(256);

		self::$enchantments[self::PROTECTION] = new Enchantment(self::PROTECTION, "%enchantment.protect.all", self::RARITY_COMMON, self::ACTIVATION_EQUIP, self::SLOT_ARMOR);
		self::$enchantments[self::FIRE_PROTECTION] = new Enchantment(self::FIRE_PROTECTION, "%enchantment.protect.fire", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_ARMOR);
		self::$enchantments[self::FEATHER_FALLING] = new Enchantment(self::FEATHER_FALLING, "%enchantment.protect.fall", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_FEET);

		self::$enchantments[self::BLAST_PROTECTION] = new Enchantment(self::BLAST_PROTECTION, "%enchantment.protect.explosion", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_ARMOR);
		self::$enchantments[self::PROJECTILE_PROTECTION] = new Enchantment(self::PROJECTILE_PROTECTION, "%enchantment.protect.projectile", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_ARMOR);
		self::$enchantments[self::THORNS] = new Enchantment(self::THORNS, "%enchantment.protect.thorns", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_SWORD);
		self::$enchantments[self::RESPIRATION] = new Enchantment(self::RESPIRATION, "%enchantment.protect.waterbrething", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_FEET);
		self::$enchantments[self::DEPTH_STRIDER] = new Enchantment(self::DEPTH_STRIDER, "%enchantment.waterspeed", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_FEET);
		self::$enchantments[self::AQUA_AFFINITY] = new Enchantment(self::AQUA_AFFINITY, "%enchantment.protect.wateraffinity", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_FEET);

		self::$enchantments[self::SHARPNESS] = new Enchantment(self::SHARPNESS, "%enchantment.weapon.sharpness", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_SWORD);
		self::$enchantments[self::SMITE] = new Enchantment(self::SMITE, "%enchantment.weapon.smite", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_SWORD);
		self::$enchantments[self::BANE_OF_ARTHROPODS] = new Enchantment(self::BANE_OF_ARTHROPODS, "%enchantment.weapon.arthropods", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_SWORD);
		self::$enchantments[self::KNOCKBACK] = new Enchantment(self::KNOCKBACK, "%enchantment.weapon.knockback", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_SWORD);
		self::$enchantments[self::FIRE_ASPECT] = new Enchantment(self::FIRE_ASPECT, "%enchantment.weapon.fireaspect", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_SWORD);
		self::$enchantments[self::LOOTING] = new Enchantment(self::LOOTING, "%enchantment.weapon.looting", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_SWORD);
		self::$enchantments[self::EFFICIENCY] = new Enchantment(self::EFFICIENCY, "%enchantment.mining.efficiency", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_TOOL);
		self::$enchantments[self::SILK_TOUCH] = new Enchantment(self::SILK_TOUCH, "%enchantment.mining.silktouch", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_TOOL);
		self::$enchantments[self::UNBREAKING] = new Enchantment(self::UNBREAKING, "%enchantment.mining.durability", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_TOOL);
		self::$enchantments[self::FORTUNE] = new Enchantment(self::FORTUNE, "%enchantment.mining.fortune", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_TOOL);
		self::$enchantments[self::POWER] = new Enchantment(self::POWER, "%enchantment.bow.power", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_BOW);
		self::$enchantments[self::PUNCH] = new Enchantment(self::PUNCH, "%enchantment.bow.knockback", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_BOW);
		self::$enchantments[self::FLAME] = new Enchantment(self::FLAME, "%enchantment.bow.flame", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_BOW);
		self::$enchantments[self::INFINITY] = new Enchantment(self::INFINITY, "%enchantment.bow.infinity", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_BOW);
		self::$enchantments[self::LUCK_OF_THE_SEA] = new Enchantment(self::LUCK_OF_THE_SEA, "%enchantment.fishing.fortune", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_FISHING_ROD);
		self::$enchantments[self::LURE] = new Enchantment(self::LURE, "%enchantment.fishing.lure", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_FISHING_ROD);
	}

	/**
	 * @param int $id
	 *
	 * @return Enchantment|null
	 */
	public static function getEnchantment(int $id){
		if(isset(self::$enchantments[$id])){
			return clone self::$enchantments[$id];
		}
		return null;
	}

	/**
	 * @param string $name
	 *
	 * @return Enchantment|null
	 */
	public static function getEnchantmentByName(string $name){
		if(defined(Enchantment::class . "::" . strtoupper($name))){
			return self::getEnchantment(constant(Enchantment::class . "::" . strtoupper($name)));
		}
		return null;
	}

	private $id;
	private $level = 1;
	private $name;
	private $rarity;
	private $activationType;
	private $slot;

	/**
	 * @param int $id
	 * @param string $name
	 * @param int $rarity
	 * @param int $activationType
	 * @param int $slot
	 */
	public function __construct(int $id, string $name, int $rarity, int $activationType, int $slot){
		$this->id = $id;
		$this->name = $name;
		$this->rarity = $rarity;
		$this->activationType = $activationType;
		$this->slot = $slot;
	}

	/**
	 * Returns the ID of this enchantment as per Minecraft PE
	 * @return int
	 */
	public function getId() : int{
		return $this->id;
	}

	/**
	 * Returns a translation key for this enchantment's name.
	 * @return string
	 */
	public function getName() : string{
		return $this->name;
	}

	/**
	 * Returns an int constant indicating how rare this enchantment type is.
	 * @return int
	 */
	public function getRarity() : int{
		return $this->rarity;
	}

	/**
	 * Returns an int constant describing what type of activation this enchantment requires. For example armor enchantments only apply when worn.
	 * @return int
	 */
	public function getActivationType() : int{
		return $this->activationType;
	}

	/**
	 * Returns an int with bitflags set to indicate what item types this enchantment can apply to.
	 * @return int
	 */
	public function getSlot() : int{
		return $this->slot;
	}

	/**
	 * Returns whether this enchantment can apply to the specified item type.
	 * @param int $slot
	 *
	 * @return bool
	 */
	public function hasSlot(int $slot) : bool{
		return ($this->slot & $slot) > 0;
	}

	/**
	 * Returns the level of the enchantment.
	 * @return int
	 */
	public function getLevel(): int{
		return $this->level;
	}

	/**
	 * Sets the level of the enchantment.
	 * @param int $level
	 *
	 * @return $this
	 */
	public function setLevel(int $level){
		$this->level = $level;

		return $this;
	}

}