<?php

declare(strict_types=1);

/**
 * All the entity classes
 */
namespace pocketmine\entity;

use pocketmine\BedrockPlayer;
use pocketmine\block\Block;
use pocketmine\block\Water;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDespawnEvent;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\entity\EntityMotionEvent;
use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\event\entity\EntitySpawnEvent;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\player\PlayerEntityInteractEvent;
use pocketmine\event\Timings;
use pocketmine\event\TimingsHandler;
use pocketmine\item\Item as ItemItem;
use pocketmine\level\format\Chunk;
use pocketmine\level\Level;
use pocketmine\level\Location;
use pocketmine\level\Position;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use pocketmine\metadata\Metadatable;
use pocketmine\metadata\MetadataValue;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\ShortTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\network\bedrock\PacketTranslator;
use pocketmine\network\bedrock\protocol\SetActorDataPacket as BedrockSetActorDataPacket;
use pocketmine\network\mcpe\protocol\AddEntityPacket;
use pocketmine\network\mcpe\protocol\EntityEventPacket;
use pocketmine\network\mcpe\protocol\MoveEntityPacket;
use pocketmine\network\mcpe\protocol\RemoveEntityPacket;
use pocketmine\network\mcpe\protocol\SetEntityDataPacket;
use pocketmine\network\mcpe\protocol\SetEntityMotionPacket;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\Server;
use function abs;
use function assert;
use function count;
use function floor;
use function get_class;
use function is_a;
use function is_array;
use function is_infinite;
use function is_nan;
use function lcg_value;

abstract class Entity extends Location implements Metadatable, EntityIds{

	public const MOTION_THRESHOLD = 0.00001;
	protected const STEP_CLIP_MULTIPLIER = 0.4;

	public const NETWORK_ID = -1;

	public const DATA_TYPE_BYTE = 0;
	public const DATA_TYPE_SHORT = 1;
	public const DATA_TYPE_INT = 2;
	public const DATA_TYPE_FLOAT = 3;
	public const DATA_TYPE_STRING = 4;
	public const DATA_TYPE_SLOT = 5;
	public const DATA_TYPE_POS = 6;
	public const DATA_TYPE_LONG = 7;
	public const DATA_TYPE_VECTOR3F = 8;

	public const DATA_FLAGS = 0;
	public const DATA_HEALTH = 1; //int (minecart/boat)
	public const DATA_VARIANT = 2; //int
	public const DATA_COLOR = 3, DATA_COLOUR = 3; //byte
	public const DATA_NAMETAG = 4; //string
	public const DATA_OWNER_EID = 5; //long
	public const DATA_TARGET_EID = 6; //long
	public const DATA_AIR = 7; //short
	public const DATA_POTION_COLOR = 8; //int (ARGB!)
	public const DATA_POTION_AMBIENT = 9; //byte
	/* 10 (byte) */
	public const DATA_HURT_TIME = 11; //int (minecart/boat)
	public const DATA_HURT_DIRECTION = 12; //int (minecart/boat)
	public const DATA_PADDLE_TIME_LEFT = 13; //float
	public const DATA_PADDLE_TIME_RIGHT = 14; //float
	public const DATA_EXPERIENCE_VALUE = 15; //int (xp orb)
	public const DATA_MINECART_DISPLAY_BLOCK = 16; //int (id | (data << 16))
	public const DATA_MINECART_DISPLAY_OFFSET = 17; //int
	public const DATA_MINECART_HAS_DISPLAY = 18; //byte (must be 1 for minecart to show block inside)

	//TODO: add more properties

	public const DATA_ENDERMAN_HELD_ITEM_ID = 23; //short
	public const DATA_ENDERMAN_HELD_ITEM_DAMAGE = 24; //short
	public const DATA_ENTITY_AGE = 25; //short

	/* 27 (byte) player-specific flags
	 * 28 (int) player "index"?
	 * 29 (block coords) bed position */
	public const DATA_FIREBALL_POWER_X = 30; //float
	public const DATA_FIREBALL_POWER_Y = 31;
	public const DATA_FIREBALL_POWER_Z = 32;
	/* 33 (unknown)
	 * 34 (float) fishing bobber
	 * 35 (float) fishing bobber
	 * 36 (float) fishing bobber */
	public const DATA_POTION_AUX_VALUE = 37; //short
	public const DATA_LEAD_HOLDER_EID = 38; //long
	public const DATA_SCALE = 39; //float
	public const DATA_INTERACTIVE_TAG = 40; //string (button text)
	public const DATA_NPC_SKIN_ID = 41; //string
	public const DATA_URL_TAG = 42; //string
	public const DATA_MAX_AIR = 43; //short
	public const DATA_MARK_VARIANT = 44; //int
	public const DATA_CONTAINER_TYPE = 45; //byte (ContainerComponent)
	public const DATA_CONTAINER_BASE_SIZE = 46; //int (ContainerComponent)
	public const DATA_CONTAINER_EXTRA_SLOTS_PER_STRENGTH = 47; //int (used for llamas, inventory size is baseSize + thisProp * strength)
	public const DATA_BLOCK_TARGET = 48; //block coords (ender crystal)
	public const DATA_WITHER_INVULNERABLE_TICKS = 49; //int
	public const DATA_WITHER_TARGET_1 = 50; //long
	public const DATA_WITHER_TARGET_2 = 51; //long
	public const DATA_WITHER_TARGET_3 = 52; //long
	/* 53 (short) */
	public const DATA_BOUNDING_BOX_WIDTH = 54; //float
	public const DATA_BOUNDING_BOX_HEIGHT = 55; //float
	public const DATA_FUSE_LENGTH = 56; //int
	public const DATA_RIDER_SEAT_POSITION = 57; //vector3f
	public const DATA_RIDER_ROTATION_LOCKED = 58; //byte
	public const DATA_RIDER_MAX_ROTATION = 59; //float
	public const DATA_RIDER_MIN_ROTATION = 60; //float
	public const DATA_AREA_EFFECT_CLOUD_RADIUS = 61; //float
	public const DATA_AREA_EFFECT_CLOUD_WAITING = 62; //int
	public const DATA_AREA_EFFECT_CLOUD_PARTICLE_ID = 63; //int
	/* 64 (int) shulker-related */
	public const DATA_SHULKER_ATTACH_FACE = 65; //byte
	/* 66 (short) shulker-related */
	public const DATA_SHULKER_ATTACH_POS = 67; //block coords
	public const DATA_TRADING_PLAYER_EID = 68; //long

	/* 70 (byte) command-block */
	public const DATA_COMMAND_BLOCK_COMMAND = 71; //string
	public const DATA_COMMAND_BLOCK_LAST_OUTPUT = 72; //string
	public const DATA_COMMAND_BLOCK_TRACK_OUTPUT = 73; //byte
	public const DATA_CONTROLLING_RIDER_SEAT_NUMBER = 74; //byte
	public const DATA_STRENGTH = 75; //int
	public const DATA_MAX_STRENGTH = 76; //int
	/* 77 (int)
	 * 78 (int) */


	public const DATA_FLAG_ONFIRE = 0;
	public const DATA_FLAG_SNEAKING = 1;
	public const DATA_FLAG_RIDING = 2;
	public const DATA_FLAG_SPRINTING = 3;
	public const DATA_FLAG_ACTION = 4;
	public const DATA_FLAG_INVISIBLE = 5;
	public const DATA_FLAG_TEMPTED = 6;
	public const DATA_FLAG_INLOVE = 7;
	public const DATA_FLAG_SADDLED = 8;
	public const DATA_FLAG_POWERED = 9;
	public const DATA_FLAG_IGNITED = 10;
	public const DATA_FLAG_BABY = 11;
	public const DATA_FLAG_CONVERTING = 12;
	public const DATA_FLAG_CRITICAL = 13;
	public const DATA_FLAG_CAN_SHOW_NAMETAG = 14;
	public const DATA_FLAG_ALWAYS_SHOW_NAMETAG = 15;
	public const DATA_FLAG_IMMOBILE = 16, DATA_FLAG_NO_AI = 16;
	public const DATA_FLAG_SILENT = 17;
	public const DATA_FLAG_WALLCLIMBING = 18;
	public const DATA_FLAG_CAN_CLIMB = 19;
	public const DATA_FLAG_SWIMMER = 20;
	public const DATA_FLAG_CAN_FLY = 21;
	public const DATA_FLAG_RESTING = 22;
	public const DATA_FLAG_SITTING = 23;
	public const DATA_FLAG_ANGRY = 24;
	public const DATA_FLAG_INTERESTED = 25;
	public const DATA_FLAG_CHARGED = 26;
	public const DATA_FLAG_TAMED = 27;
	public const DATA_FLAG_LEASHED = 28;
	public const DATA_FLAG_SHEARED = 29;
	public const DATA_FLAG_GLIDING = 30;
	public const DATA_FLAG_ELDER = 31;
	public const DATA_FLAG_MOVING = 32;
	public const DATA_FLAG_BREATHING = 33;
	public const DATA_FLAG_CHESTED = 34;
	public const DATA_FLAG_STACKABLE = 35;
	public const DATA_FLAG_SHOWBASE = 36;
	public const DATA_FLAG_REARING = 37;
	public const DATA_FLAG_VIBRATING = 38;
	public const DATA_FLAG_IDLING = 39;
	public const DATA_FLAG_EVOKER_SPELL = 40;
	public const DATA_FLAG_CHARGE_ATTACK = 41;
	public const DATA_FLAG_WASD_CONTROLLED = 42;

	public const DATA_FLAG_LINGER = 45;

	public static $entityCount = 1;
	/** @var Entity[] */
	private static $knownEntities = [];
	private static $shortNames = [];

	public static function init(){
		Entity::registerEntity(Arrow::class);
		Entity::registerEntity(FallingSand::class);
		Entity::registerEntity(Item::class);
		Entity::registerEntity(PrimedTNT::class);
		Entity::registerEntity(Snowball::class);
		Entity::registerEntity(Egg::class);
		Entity::registerEntity(EnderPearl::class);
		Entity::registerEntity(Squid::class);
		Entity::registerEntity(SplashPotion::class);
		Entity::registerEntity(Villager::class);
		Entity::registerEntity(Zombie::class);
		Entity::registerEntity(FishingHook::class);

		Entity::registerEntity(Human::class, true);
	}

	/**
	 * @var Player[]
	 */
	protected $hasSpawned = [];

	protected $id;

	protected $dataProperties = [
		self::DATA_FLAGS => [self::DATA_TYPE_LONG, 0],
		self::DATA_AIR => [self::DATA_TYPE_SHORT, 400],
		self::DATA_MAX_AIR => [self::DATA_TYPE_SHORT, 400],
		self::DATA_NAMETAG => [self::DATA_TYPE_STRING, ""],
		self::DATA_LEAD_HOLDER_EID => [self::DATA_TYPE_LONG, -1],
		self::DATA_SCALE => [self::DATA_TYPE_FLOAT, 1],
		self::DATA_COLOR => [self::DATA_TYPE_BYTE, 0],
	];

	protected $changedDataProperties = [];

	public $passenger = null;
	public $vehicle = null;

	/** @var Chunk */
	public $chunk;

	protected $lastDamageCause = null;

	/** @var Block[] */
	protected $blocksAround = [];

	public $lastX = null;
	public $lastY = null;
	public $lastZ = null;

	public $motionX;
	public $motionY;
	public $motionZ;
	/** @var Vector3 */
	public $temporalVector;
	public $lastMotionX;
	public $lastMotionY;
	public $lastMotionZ;

	/** @var bool */
	protected $forceMovementUpdate = false;

	public $lastYaw;
	public $lastPitch;

	/** @var AxisAlignedBB */
	public $boundingBox;
	public $onGround;
	public $inBlock = false;
	public $positionChanged;
	public $motionChanged;
	public $deadTicks = 0;
	protected $age = 0;

	public $height;
	public $width;

	public $eyeHeight = null;

	protected $baseOffset = 0.0;

	/** @var int */
	private $health = 20;
	private $maxHealth = 20;

	protected $ySize = 0;
	protected $stepHeight = 0;
	public $keepMovement = false;

	/** @var float */
	public $fallDistance = 0.0;
	public $ticksLived = 0;
	public $lastUpdate;
	public $maxFireTicks;
	public $fireTicks = 0;
	public $namedtag;
	public $canCollide = true;

	protected $isStatic = false;

	public $isCollided = false;
	public $isCollidedHorizontally = false;
	public $isCollidedVertically = false;

	public $noDamageTicks;
	protected $justCreated;
	private $invulnerable;

	/** @var AttributeMap */
	protected $attributeMap;

	public $gravity;
	public $drag;

	/** @var Server */
	protected $server;

	public $closed = false;
	/** @var bool */
	private $needsDespawn = false;

	/** @var TimingsHandler */
	protected $timings;
	protected $isPlayer = false;


	public function __construct(Level $level, CompoundTag $nbt){
		$this->timings = Timings::getEntityTimings($this);

		$this->isPlayer = $this instanceof Player;

		$this->temporalVector = new Vector3();

		if($this->eyeHeight === null){
			$this->eyeHeight = $this->height / 2 + 0.1;
		}

		$this->id = Entity::$entityCount++;
		$this->justCreated = true;
		$this->namedtag = $nbt;

		$loc = EntityDataHelper::parseLocation($this->namedtag, $level);

		$this->chunk = $level->getChunk($loc->x >> 4, $loc->z >> 4, true);
		assert($this->chunk !== null);
		$this->setLevel($level);
		$this->server = $level->getServer();

		$this->setPositionAndRotation($loc, $loc->yaw, $loc->pitch);

		$this->boundingBox = new AxisAlignedBB(0, 0, 0, 0, 0, 0);
		$this->recalculateBoundingBox();

		$this->setMotion(EntityDataHelper::parseVec3($nbt, "Motion", true));

		$this->resetLastMovements();

		assert(!is_nan($this->x) and !is_infinite($this->x) and !is_nan($this->y) and !is_infinite($this->y) and !is_nan($this->z) and !is_infinite($this->z));

		if(!$this->namedtag->hasTag("FallDistance", FloatTag::class)){
			$this->namedtag->setFloat("FallDistance", 0.0);
		}
		$this->fallDistance = $this->namedtag->getFloat("FallDistance");

		if(!$this->namedtag->hasTag("Fire", ShortTag::class)){
			$this->namedtag->setShort("Fire", 0);
		}
		$this->fireTicks = $this->namedtag->getShort("Fire");

		if(!$this->namedtag->hasTag("Air", ShortTag::class)){
			$this->namedtag->setShort("Air", 300);
		}
		$this->setDataProperty(self::DATA_AIR, self::DATA_TYPE_SHORT, $this->namedtag->getShort("Air"), false);

		if(!$this->namedtag->hasTag("OnGround", ByteTag::class)){
			$this->namedtag->setByte("OnGround", 0);
		}
		$this->onGround = $this->namedtag->getByte("OnGround") !== 0;

		if(!$this->namedtag->hasTag("Invulnerable", ByteTag::class)){
			$this->namedtag->setByte("Invulnerable", 0);
		}
		$this->invulnerable = $this->namedtag->getByte("Invulnerable") !== 0;

		$this->attributeMap = new AttributeMap();
		$this->addAttributes();

		$this->chunk->addEntity($this);
		$this->level->addEntity($this);
		$this->initEntity();
		$this->lastUpdate = $this->server->getTick();
		(new EntitySpawnEvent($this))->call();

		$this->scheduleUpdate();

	}

	/**
	 * @return Server
	 */
	public function getServer() : Server{
		return $this->server;
	}

	/**
	 * @return string
	 */
	public function getNameTag(){
		return $this->getDataProperty(self::DATA_NAMETAG);
	}

	/**
	 * @return bool
	 */
	public function isNameTagVisible(){
		return $this->getDataFlag(self::DATA_FLAGS, self::DATA_FLAG_CAN_SHOW_NAMETAG);
	}

	/**
	 * @return bool
	 */
	public function isNameTagAlwaysVisible(){
		return $this->getDataFlag(self::DATA_FLAGS, self::DATA_FLAG_ALWAYS_SHOW_NAMETAG);
	}


	/**
	 * @param string $name
	 */
	public function setNameTag($name){
		$this->setDataProperty(self::DATA_NAMETAG, self::DATA_TYPE_STRING, $name);
	}

	/**
	 * @param bool $value
	 */
	public function setNameTagVisible($value = true){
		$this->setDataFlag(self::DATA_FLAGS, self::DATA_FLAG_CAN_SHOW_NAMETAG, $value);
	}

	/**
	 * @param bool $value
	 */
	public function setNameTagAlwaysVisible($value = true){
		$this->setDataFlag(self::DATA_FLAGS, self::DATA_FLAG_ALWAYS_SHOW_NAMETAG, $value);
	}

	/**
	 * @return float
	 */
	public function getScale() : float{
		return $this->getDataProperty(self::DATA_SCALE);
	}

	/**
	 * @param float $value
	 */
	public function setScale(float $value){
		$multiplier = $value / $this->getScale();

		$this->width *= $multiplier;
		$this->height *= $multiplier;
		$this->eyeHeight *= $multiplier;

		$this->recalculateBoundingBox();

		$this->setDataProperty(self::DATA_SCALE, self::DATA_TYPE_FLOAT, $value);
	}

	public function recalculateBoundingBox() : void{
		$halfWidth = $this->width / 2;

		if($this->boundingBox === null){
			$this->boundingBox = new AxisAlignedBB(
				$this->x - $halfWidth,
				$this->y + $this->ySize,
				$this->z - $halfWidth,
				$this->x + $halfWidth,
				$this->y + $this->height + $this->ySize,
				$this->z + $halfWidth
			);
		}else{
			$this->boundingBox->setBounds(
				$this->x - $halfWidth,
				$this->y + $this->ySize,
				$this->z - $halfWidth,
				$this->x + $halfWidth,
				$this->y + $this->height + $this->ySize,
				$this->z + $halfWidth
			);
		}
	}

	public function getBoundingBox(){
		return $this->boundingBox;
	}


	public function isSneaking(){
		return $this->getDataFlag(self::DATA_FLAGS, self::DATA_FLAG_SNEAKING);
	}

	public function setSneaking($value = true){
		$this->setDataFlag(self::DATA_FLAGS, self::DATA_FLAG_SNEAKING, (bool) $value);
	}

	public function isSprinting(){
		return $this->getDataFlag(self::DATA_FLAGS, self::DATA_FLAG_SPRINTING);
	}

	public function setSprinting($value = true){
		if($value !== $this->isSprinting()){
			$this->setDataFlag(self::DATA_FLAGS, self::DATA_FLAG_SPRINTING, (bool) $value);
			$attr = $this->attributeMap->getAttribute(Attribute::MOVEMENT_SPEED);
			$attr->setValue($value ? ($attr->getValue() * 1.3) : ($attr->getValue() / 1.3), false, true);
		}
	}

	public function isGliding(){
		return $this->getDataFlag(self::DATA_FLAGS, self::DATA_FLAG_GLIDING);
	}

	public function setGliding($value = true){
		$this->setDataFlag(self::DATA_FLAGS, self::DATA_FLAG_GLIDING, (bool) $value);
	}

	public function isImmobile() : bool{
		return $this->getDataFlag(self::DATA_FLAGS, self::DATA_FLAG_IMMOBILE);
	}

	public function setImmobile($value = true){
		$this->setDataFlag(self::DATA_FLAGS, self::DATA_FLAG_IMMOBILE, $value);
	}

	public function isInvisible() : bool{
		return $this->getDataFlag(self::DATA_FLAGS, self::DATA_FLAG_INVISIBLE);
	}

	public function setInvisible($value = true){
		$this->setDataFlag(self::DATA_FLAGS, self::DATA_FLAG_INVISIBLE, $value);
	}

	/**
	 * Returns whether the entity is able to climb blocks such as ladders or vines.
	 * @return bool
	 */
	public function canClimb() : bool{
		return $this->getDataFlag(self::DATA_FLAGS, self::DATA_FLAG_CAN_CLIMB);
	}

	/**
	 * Sets whether the entity is able to climb climbable blocks.
	 * @param bool $value
	 */
	public function setCanClimb(bool $value){
		$this->setDataFlag(self::DATA_FLAGS, self::DATA_FLAG_CAN_CLIMB, $value);
	}

	/**
	 * Returns whether this entity is climbing a block. By default this is only true if the entity is climbing a ladder or vine or similar block.
	 *
	 * @return bool
	 */
	public function canClimbWalls() : bool{
		return $this->getDataFlag(self::DATA_FLAGS, self::DATA_FLAG_WALLCLIMBING);
	}

	/**
	 * Sets whether the entity is climbing a block. If true, the entity can climb anything.
	 *
	 * @param bool $value
	 */
	public function setCanClimbWalls(bool $value = true){
		$this->setDataFlag(self::DATA_FLAGS, self::DATA_FLAG_WALLCLIMBING, $value);
	}

	/**
	 * Returns the entity ID of the owning entity, or null if the entity doesn't have an owner.
	 * @return int|string|null
	 */
	public function getOwningEntityId(){
		return $this->getDataProperty(self::DATA_OWNER_EID);
	}

	/**
	 * Returns the owning entity, or null if the entity was not found.
	 * @return Entity|null
	 */
	public function getOwningEntity(){
		$eid = $this->getOwningEntityId();
		if($eid !== null){
			return $this->server->findEntity($eid);
		}

		return null;
	}

	/**
	 * Sets the owner of the entity.
	 *
	 * @param Entity $owner
	 *
	 * @throws \InvalidArgumentException if the supplied entity is not valid
	 */
	public function setOwningEntity(Entity $owner){
		if($owner->closed){
			throw new \InvalidArgumentException("Supplied owning entity is garbage and cannot be used");
		}

		$this->setDataProperty(self::DATA_OWNER_EID, self::DATA_TYPE_LONG, $owner->getId());
	}

	/**
	 * Returns the entity ID of the entity's target, or null if it doesn't have a target.
	 * @return int|string|null
	 */
	public function getTargetEntityId(){
		return $this->getDataProperty(self::DATA_TARGET_EID);
	}

	/**
	 * Returns the entity's target entity, or null if not found.
	 * This is used for things like hostile mobs attacking entities, and for fishing rods reeling hit entities in.
	 *
	 * @return Entity|null
	 */
	public function getTargetEntity(){
		$eid = $this->getTargetEntityId();
		if($eid !== null){
			return $this->server->findEntity($eid);
		}

		return null;
	}

	/**
	 * Sets the entity's target entity.
	 *
	 * @param Entity $target
	 *
	 * @throws \InvalidArgumentException if the target entity is not valid
	 */
	public function setTargetEntity(Entity $target){
		if($target->closed){
			throw new \InvalidArgumentException("Supplied target entity is garbage and cannot be used");
		}

		$this->setDataProperty(self::DATA_TARGET_EID, self::DATA_TYPE_LONG, $target->getId());
	}

	/**
	 * @deprecated
	 *
	 * @return Effect[]
	 */
	public function getEffects() : array{
		return [];
	}

	/**
	 * @deprecated
	 */
	public function removeAllEffects(){

	}

	/**
	 * @deprecated
	 *
	 * @param int $effectId
	 */
	public function removeEffect(int $effectId){

	}

	/**
	 * @deprecated
	 *
	 * @param int $effectId
	 *
	 * @return Effect|null
	 */
	public function getEffect(int $effectId){
		return null;
	}

	/**
	 * @deprecated
	 *
	 * @param int $effectId
	 *
	 * @return bool
	 */
	public function hasEffect(int $effectId) : bool{
		return false;
	}

	/**
	 * @deprecated
	 *
	 * @param Effect $effect
	 */
	public function addEffect(Effect $effect){
		throw new \BadMethodCallException("Cannot add effects to non-living entities");
	}

	/**
	 * @param int|string  $type
	 * @param Level       $level
	 * @param CompoundTag $nbt
	 * @param             $args
	 *
	 * @return Entity|null
	 */
	public static function createEntity($type, Level $level, CompoundTag $nbt, ...$args){
		if(isset(self::$knownEntities[$type])){
			$class = self::$knownEntities[$type];
			return new $class($level, $nbt, ...$args);
		}

		return null;
	}

	public static function registerEntity($className, $force = false){
		$class = new \ReflectionClass($className);
		if(is_a($className, Entity::class, true) and !$class->isAbstract()){
			if($className::NETWORK_ID !== -1){
				self::$knownEntities[$className::NETWORK_ID] = $className;
			}elseif(!$force){
				return false;
			}

			self::$knownEntities[$class->getShortName()] = $className;
			self::$shortNames[$className] = $class->getShortName();
			return true;
		}

		return false;
	}

	/**
	 * Helper function which creates minimal NBT needed to spawn an entity.
	 *
	 * @param Vector3      $pos
	 * @param Vector3|null $motion
	 * @param float        $yaw
	 * @param float        $pitch
	 *
	 * @return CompoundTag
	 */
	public static function createBaseNBT(Vector3 $pos, ?Vector3 $motion = null, float $yaw = 0.0, float $pitch = 0.0) : CompoundTag{
		return EntityDataHelper::createBaseNBT($pos, $motion, $yaw, $pitch);
	}

	/**
	 * Returns the short save name
	 *
	 * @return string
	 */
	public function getSaveId(){
		return self::$shortNames[static::class];
	}

	public function saveNBT(){
		if(!($this instanceof Player)){
			$this->namedtag->setString("id", $this->getSaveId());
			if($this->getNameTag() !== ""){
				$this->namedtag->setString("CustomName", $this->getNameTag());
				$this->namedtag->setByte("CustomNameVisible", $this->isNameTagVisible() ? 1 : 0);
				$this->namedtag->setByte("CustomNameAlwaysVisible", $this->isNameTagAlwaysVisible() ? 1 : 0);
			}else{
				$this->namedtag->removeTag("CustomName", "CustomNameVisible", "CustomNameAlwaysVisible");
			}
		}

		$this->namedtag->setTag("Pos", new ListTag([
			new DoubleTag($this->x),
			new DoubleTag($this->y),
			new DoubleTag($this->z)
		]));

		$this->namedtag->setTag("Motion", new ListTag([
			new DoubleTag($this->motionX),
			new DoubleTag($this->motionY),
			new DoubleTag($this->motionZ)
		]));

		$this->namedtag->setTag("Rotation", new ListTag([
			new FloatTag($this->yaw),
			new FloatTag($this->pitch)
		]));

		$this->namedtag->setFloat("FallDistance", $this->fallDistance);
		$this->namedtag->setShort("Fire", $this->fireTicks);
		$this->namedtag->setShort("Air", $this->getDataProperty(self::DATA_AIR));
		$this->namedtag->setByte("OnGround", $this->onGround ? 1 : 0);
		$this->namedtag->setByte("Invulnerable", $this->invulnerable ? 1 : 0);
	}

	protected function initEntity(){
		assert($this->namedtag instanceof CompoundTag);

		if($this->namedtag->hasTag("CustomName", StringTag::class)){
			$this->setNameTag($this->namedtag->getString("CustomName"));
			if($this->namedtag->hasTag("CustomNameVisible", ByteTag::class)){
				$this->setNameTagVisible($this->namedtag->getByte("CustomNameVisible") > 0);
			}

			if($this->namedtag->hasTag("CustomNameAlwaysVisible", ByteTag::class)){
				$this->setNameTagAlwaysVisible($this->namedtag->getByte("CustomNameAlwaysVisible") > 0);
			}
		}

		$this->scheduleUpdate();
	}

	protected function addAttributes(){

	}

	/**
	 * @return Player[]
	 */
	public function getViewers(){
		return $this->hasSpawned;
	}

	/**
	 * Called by spawnTo() to send whatever packets needed to spawn the entity to the client.
	 *
	 * @param Player $player
	 */
	protected function sendSpawnPacket(Player $player) : void{
		if(static::NETWORK_ID !== -1){
			$pk = new AddEntityPacket();
			$pk->entityRuntimeId = $this->getId();
			$pk->type = static::NETWORK_ID;
			$pk->x = $this->x;
			$pk->y = $this->y;
			$pk->z = $this->z;
			$pk->speedX = $this->motionX;
			$pk->speedY = $this->motionY;
			$pk->speedZ = $this->motionZ;
			$pk->yaw = $this->yaw;
			$pk->pitch = $this->pitch;
			$pk->metadata = $this->dataProperties;

			$player->sendDataPacket($pk);
		}
	}

	/**
	 * @param Player $player
	 */
	public function spawnTo(Player $player) : void{
		if(!isset($this->hasSpawned[$player->getLoaderId()]) and isset($player->usedChunks[Level::chunkHash($this->chunk->getX(), $this->chunk->getZ())])){
			$this->hasSpawned[$player->getLoaderId()] = $player;

			$this->sendSpawnPacket($player);
		}
	}

	/**
	 * @deprecated
	 *
	 * @param Player $player
	 */
	public function sendPotionEffects(Player $player){

	}

	/**
	 * @param Player[]|Player $player
	 * @param array           $data Properly formatted entity data, defaults to everything
	 */
	public function sendData($player, array $data = null){
		if(!is_array($player)){
			$player = [$player];
		}

		$pk = new SetEntityDataPacket();
		$pk->entityRuntimeId = $this->getId();
		$pk->metadata = $data ?? $this->dataProperties;

		$bk = new BedrockSetActorDataPacket();
		$bk->actorRuntimeId = $this->getId();
		$bk->metadata = PacketTranslator::translateMetadata($pk->metadata);

		foreach($player as $p){
			if($p === $this){
				continue;
			}
			$p->sendDataPacket($p instanceof BedrockPlayer ? clone $bk : clone $pk);
		}

		if($this instanceof Player){
			$this->sendDataPacket($this instanceof BedrockPlayer ? $bk : $pk);
		}
	}

	/**
	 * @param Player $player
	 * @param bool   $send
	 */
	public function despawnFrom(Player $player, bool $send = true){
		if(isset($this->hasSpawned[$player->getLoaderId()])){
			if($send){
				$pk = new RemoveEntityPacket();
				$pk->entityUniqueId = $this->id;
				$player->sendDataPacket($pk);
			}
			unset($this->hasSpawned[$player->getLoaderId()]);
		}
	}

	public function onInteract(Player $player, ItemItem $item) : bool{
		$ev = new PlayerEntityInteractEvent($player, $this, $item);
		$ev->call();

		if($ev->isCancelled()){
			return false;
		}
		return true;
	}

	/**
	 * @param float             $damage
	 * @param EntityDamageEvent $source
	 *
	 */
	public function attack($damage, EntityDamageEvent $source){
		$this->applyDamageModifiers($source);

		$source->call();
		if($source->isCancelled()){
			return;
		}

		$damage = $source->getFinalDamage();

		$this->setLastDamageCause($source);

		$this->setHealth($this->getHealth() - $damage);

		if($this->isAlive()){
			$this->applyPostDamageEffects($source);
		}
	}

	/**
	 * Called prior to EntityDamageEvent execution to apply modifications to the event's damage, such as reduction due
	 * to effects or armour.
	 *
	 * @param EntityDamageEvent $source
	 */
	public function applyDamageModifiers(EntityDamageEvent $source) : void{
		$source->setDamage(-min($this->getAbsorption(), $source->getFinalDamage()), EntityDamageEvent::MODIFIER_ABSORPTION);
	}

	/**
	 * Called after EntityDamageEvent execution to apply post-hurt effects, such as reducing absorption or modifying
	 * armour durability.
	 * This will not be called by damage sources causing death.
	 *
	 * @param EntityDamageEvent $source
	 */
	protected function applyPostDamageEffects(EntityDamageEvent $source) : void{
		$this->setAbsorption(max(0, $this->getAbsorption() + $source->getDamage(EntityDamageEvent::MODIFIER_ABSORPTION)));
	}

	/**
	 * @param float                   $amount
	 * @param EntityRegainHealthEvent $source
	 *
	 */
	public function heal($amount, EntityRegainHealthEvent $source){
		$source->call();
		if($source->isCancelled()){
			return;
		}

		$this->setHealth($this->getHealth() + $source->getAmount());
	}

	/**
	 * @return int
	 */
	public function getHealth(){
		return $this->health;
	}

	public function isAlive(){
		return $this->health > 0;
	}

	/**
	 * Sets the health of the Entity. This won't send any update to the players
	 *
	 * @param int $amount
	 */
	public function setHealth($amount){
		$amount = (int) $amount;
		if($amount === $this->health){
			return;
		}

		if($amount <= 0){
			if($this->isAlive()){
				$this->kill();
			}
		}elseif($amount <= $this->getMaxHealth() or $amount < $this->health){
			$this->health = (int) $amount;
		}else{
			$this->health = $this->getMaxHealth();
		}
	}

	public function getAbsorption() : float{
		return 0;
	}

	public function setAbsorption(float $absorption){

	}

	/**
	 * @param EntityDamageEvent $type
	 */
	public function setLastDamageCause(EntityDamageEvent $type){
		$this->lastDamageCause = $type;
	}

	/**
	 * @return EntityDamageEvent|null
	 */
	public function getLastDamageCause(){
		return $this->lastDamageCause;
	}

	public function getAttributeMap(){
		return $this->attributeMap;
	}

	/**
	 * @return int
	 */
	public function getMaxHealth(){
		return $this->maxHealth;
	}

	/**
	 * @param int $amount
	 */
	public function setMaxHealth($amount){
		$this->maxHealth = (int) $amount;
	}

	public function canCollideWith(Entity $entity){
		return !$this->justCreated and $entity !== $this and !($entity instanceof Player and $entity->isSpectator());
	}

	protected function checkObstruction($x, $y, $z){
		if(count($this->level->getCollisionCubes($this, $this->getBoundingBox(), false)) === 0){
			return false;
		}

		$i = (int) floor($x);
		$j = (int) floor($y);
		$k = (int) floor($z);

		$diffX = $x - $i;
		$diffY = $y - $j;
		$diffZ = $z - $k;

		if(Block::$solid[$this->level->getBlockIdAt($i, $j, $k)]){
			$flag = !Block::$solid[$this->level->getBlockIdAt($i - 1, $j, $k)];
			$flag1 = !Block::$solid[$this->level->getBlockIdAt($i + 1, $j, $k)];
			$flag2 = !Block::$solid[$this->level->getBlockIdAt($i, $j - 1, $k)];
			$flag3 = !Block::$solid[$this->level->getBlockIdAt($i, $j + 1, $k)];
			$flag4 = !Block::$solid[$this->level->getBlockIdAt($i, $j, $k - 1)];
			$flag5 = !Block::$solid[$this->level->getBlockIdAt($i, $j, $k + 1)];

			$direction = -1;
			$limit = 9999;

			if($flag){
				$limit = $diffX;
				$direction = 0;
			}

			if($flag1 and 1 - $diffX < $limit){
				$limit = 1 - $diffX;
				$direction = 1;
			}

			if($flag2 and $diffY < $limit){
				$limit = $diffY;
				$direction = 2;
			}

			if($flag3 and 1 - $diffY < $limit){
				$limit = 1 - $diffY;
				$direction = 3;
			}

			if($flag4 and $diffZ < $limit){
				$limit = $diffZ;
				$direction = 4;
			}

			if($flag5 and 1 - $diffZ < $limit){
				$direction = 5;
			}

			$force = lcg_value() * 0.2 + 0.1;

			if($direction === 0){
				$this->motionX = -$force;

				return true;
			}

			if($direction === 1){
				$this->motionX = $force;

				return true;
			}

			if($direction === 2){
				$this->motionY = -$force;

				return true;
			}

			if($direction === 3){
				$this->motionY = $force;

				return true;
			}

			if($direction === 4){
				$this->motionZ = -$force;

				return true;
			}

			if($direction === 5){
				$this->motionZ = $force;

				return true;
			}
		}

		return false;
	}

	public function entityBaseTick($tickDiff = 1){
		//TODO: check vehicles

		$this->justCreated = false;

		if(!$this->isAlive()){
			$this->removeAllEffects();
			$this->despawnFromAll();
			if(!$this->isPlayer){
				$this->close();
			}

			return false;
		}

		if(count($this->changedDataProperties) > 0){
			$this->sendData($this->hasSpawned, $this->changedDataProperties);
			$this->changedDataProperties = [];
		}

		$hasUpdate = false;

		$this->checkBlockCollision();

		if($this->y <= -16 and $this->isAlive()){
			$ev = new EntityDamageEvent($this, EntityDamageEvent::CAUSE_VOID, 10);
			$this->attack($ev->getFinalDamage(), $ev);
			$hasUpdate = true;
		}

		if($this->isOnFire()){
			$hasUpdate = ($hasUpdate || $this->doOnFireTick($tickDiff));
		}

		if($this->noDamageTicks > 0){
			$this->noDamageTicks -= $tickDiff;
			if($this->noDamageTicks < 0){
				$this->noDamageTicks = 0;
			}
		}

		$this->age += $tickDiff;
		$this->ticksLived += $tickDiff;

		return $hasUpdate;
	}

	protected function doOnFireTick(int $tickDiff = 1) : bool{
		if($this->isFireProof() and $this->fireTicks > 1){
			$this->fireTicks = 1;
		}else{
			$this->fireTicks -= $tickDiff;
		}



		if(($this->fireTicks % 20 === 0) or $tickDiff > 20){
			$this->dealFireDamage();
		}

		if(!$this->isOnFire()){
			$this->extinguish();
		}else{
			return true;
		}

		return false;
	}

	/**
	 * Called to deal damage to entities when they are on fire.
	 */
	protected function dealFireDamage(){
		$ev = new EntityDamageEvent($this, EntityDamageEvent::CAUSE_FIRE_TICK, 1);
		$this->attack($ev->getFinalDamage(), $ev);
	}

	protected function updateMovement(){
		//TODO: hack for client-side AI interference: prevent client sided movement when motion is 0
		$this->setImmobile($this->motionX == 0 and $this->motionY == 0 and $this->motionZ == 0);

		$diffPosition = ($this->x - $this->lastX) ** 2 + ($this->y - $this->lastY) ** 2 + ($this->z - $this->lastZ) ** 2;
		$diffRotation = ($this->yaw - $this->lastYaw) ** 2 + ($this->pitch - $this->lastPitch) ** 2;

		$diffMotion = ($this->motionX - $this->lastMotionX) ** 2 + ($this->motionY - $this->lastMotionY) ** 2 + ($this->motionZ - $this->lastMotionZ) ** 2;

		if($diffPosition > 0.0001 or $diffRotation > 1.0){
			$this->lastX = $this->x;
			$this->lastY = $this->y;
			$this->lastZ = $this->z;

			$this->lastYaw = $this->yaw;
			$this->lastPitch = $this->pitch;

			$this->broadcastMovement();
		}

		if($diffMotion > 0.0025 or ($diffMotion > 0.0001 and $this->getMotion()->lengthSquared() <= 0.0001)){ //0.05 ** 2
			$this->lastMotionX = $this->motionX;
			$this->lastMotionY = $this->motionY;
			$this->lastMotionZ = $this->motionZ;

            $this->broadcastMotion();
		}
	}

	public function onUpdate($currentTick){
		if($this->closed){
			return false;
		}

		$tickDiff = $currentTick - $this->lastUpdate;
		if($tickDiff <= 0){
			if(!$this->justCreated){
				$this->server->getLogger()->debug("Expected tick difference of at least 1, got $tickDiff for " . get_class($this));
			}

			return true;
		}

		if(!$this->isAlive()){
			$this->deadTicks += $tickDiff;
			if($this->deadTicks >= 10){
				$this->despawnFromAll();
				if(!$this->isPlayer){
					$this->close();
				}
			}
			return $this->deadTicks < 10;
		}

		$this->lastUpdate = $currentTick;

		$this->timings->startTiming();

		Timings::$timerEntityBaseTick->startTiming();
		$hasUpdate = $this->entityBaseTick($tickDiff);
		Timings::$timerEntityBaseTick->stopTiming();

		if($this->hasMovementUpdate()){
			$this->tryChangeMovement();

			if(abs($this->motionX) <= self::MOTION_THRESHOLD){
				$this->motionX = 0.0;
			}
			if(abs($this->motionY) <= self::MOTION_THRESHOLD){
				$this->motionY = 0.0;
			}
			if(abs($this->motionZ) <= self::MOTION_THRESHOLD){
				$this->motionZ = 0.0;
			}

			if($this->motionX != 0 or $this->motionY != 0 or $this->motionZ != 0){
				$this->move($this->motionX, $this->motionY, $this->motionZ);
			}

			$this->forceMovementUpdate = false;
		}

		$this->updateMovement();

		$this->timings->stopTiming();

		return ($hasUpdate or $this->hasMovementUpdate());
	}

	public function onNearbyBlockChange() : void{
		$this->setForceMovementUpdate();
		$this->scheduleUpdate();
	}

	final public function scheduleUpdate(){
		$this->level->updateEntities[$this->id] = $this;
	}

	public function isOnFire(){
		return $this->fireTicks > 0;
	}

	public function setOnFire($seconds){
		$ticks = $seconds * 20;
		if($ticks > $this->fireTicks){
			$this->fireTicks = $ticks;
		}

		$this->setDataFlag(self::DATA_FLAGS, self::DATA_FLAG_ONFIRE, true);
	}

	/**
	 * @return int
	 */
	public function getFireTicks(){
		return $this->fireTicks;
	}

	public function extinguish(){
		$this->fireTicks = 0;
		$this->setDataFlag(self::DATA_FLAGS, self::DATA_FLAG_ONFIRE, false);
	}

	public function isFireProof() : bool{
		return false;
	}

	public function getDirection(){
		$rotation = ($this->yaw - 90) % 360;
		if($rotation < 0){
			$rotation += 360.0;
		}
		if((0 <= $rotation and $rotation < 45) or (315 <= $rotation and $rotation < 360)){
			return 2; //North
		}elseif(45 <= $rotation and $rotation < 135){
			return 3; //East
		}elseif(135 <= $rotation and $rotation < 225){
			return 0; //South
		}elseif(225 <= $rotation and $rotation < 315){
			return 1; //West
		}else{
			return null;
		}
	}

	public function canTriggerWalking(){
		return true;
	}

	/**
	 * Flags the entity as needing a movement update on the next tick. Setting this forces a movement update even if the
	 * entity's motion is zero. Used to trigger movement updates when blocks change near entities.
	 *
	 * @param bool $value
	 */
	final public function setForceMovementUpdate(bool $value = true) : void{
		$this->forceMovementUpdate = $value;
		$this->onGround = false;

		$this->blocksAround = null;
	}
	/**
	 * Returns whether the entity needs a movement update on the next tick.
	 * @return bool
	 */
	public function hasMovementUpdate() : bool{
		return (
			$this->forceMovementUpdate or
			$this->motionX != 0 or
			$this->motionY != 0 or
			$this->motionZ != 0 or
			!$this->onGround
		);
	}

	public function resetFallDistance(){
		$this->fallDistance = 0.0;
	}

	/**
	 * @param float $distanceThisTick
	 * @param bool  $onGround
	 */
	protected function updateFallState(float $distanceThisTick, bool $onGround){
		if($onGround === true){
			if($this->fallDistance > 0){
				$this->fall($this->fallDistance);
				$this->resetFallDistance();
			}
		}elseif($distanceThisTick < $this->fallDistance){
			//we've fallen some distance (distanceThisTick is negative)
			//or we ascended back towards where fall distance was measured from initially (distanceThisTick is positive but less than existing fallDistance)
			$this->fallDistance -= $distanceThisTick;
		}else{
			//we ascended past the apex where fall distance was originally being measured from
			//reset it so it will be measured starting from the new, higher position
			$this->fallDistance = 0;
		}
	}

	/**
	 * Called when a falling entity hits the ground.
	 *
	 * @param float $fallDistance
	 */
	public function fall(float $fallDistance){

	}

	public function handleLavaMovement(){ //TODO

	}

	public function getEyeHeight(){
		return $this->eyeHeight;
	}

	public function moveFlying(){ //TODO

	}

	public function onCollideWithPlayer(Human $entityPlayer){

	}

	protected function switchLevel(Level $targetLevel){
		if($this->closed){
			return false;
		}

		if($this->isValid()){
			$ev = new EntityLevelChangeEvent($this, $this->level, $targetLevel);
			$ev->call();
			if($ev->isCancelled()){
				return false;
			}

			$this->level->removeEntity($this);
			if($this->chunk !== null){
				$this->chunk->removeEntity($this);
			}
			$this->despawnFromAll();
		}

		$this->setLevel($targetLevel);
		$this->level->addEntity($this);
		$this->chunk = null;

		return true;
	}

	public function getPosition(){
		return new Position($this->x, $this->y, $this->z, $this->level);
	}

	public function getLocation(){
		return new Location($this->x, $this->y, $this->z, $this->yaw, $this->pitch, $this->level);
	}

	public function isInsideOfWater(){
		$block = $this->level->getBlockAt((int) floor($this->x), (int) floor($y = ($this->y + $this->getEyeHeight())), (int) floor($this->z));

		if($block instanceof Water){
			$f = ($block->y + 1) - ($block->getFluidHeightPercent() - 0.1111111);
			return $y < $f;
		}

		return false;
	}

	public function isInsideOfSolid(){
		$block = $this->level->getBlockAt((int) floor($this->x), (int) floor($y = ($this->y + $this->getEyeHeight())), (int) floor($this->z));

		$bb = $block->getBoundingBox();

		return $block->isSolid() and !$block->isTransparent() and $block->collidesWithBB($this->getBoundingBox());
	}

	public function fastMove($dx, $dy, $dz){
		$this->blocksAround = null;

		if($dx == 0 and $dz == 0 and $dy == 0){
			return true;
		}

		Timings::$entityMoveTimer->startTiming();

		$newBB = $this->boundingBox->getOffsetBoundingBox($dx, $dy, $dz);

		$list = $this->level->getCollisionCubes($this, $newBB, false);

		if(count($list) === 0){
			$this->boundingBox = $newBB;
		}

		$this->x = ($this->boundingBox->minX + $this->boundingBox->maxX) / 2;
		$this->y = $this->boundingBox->minY - $this->ySize;
		$this->z = ($this->boundingBox->minZ + $this->boundingBox->maxZ) / 2;

		$this->checkChunks();
		$this->checkBlockCollision();

		if(!$this->onGround or $dy != 0){
			$bb = clone $this->boundingBox;
			$bb->minY = $this->y - 0.01;
			$bb->maxY = $this->y + 0.01;

			if(count($this->level->getCollisionBlocks($bb, true)) > 0){
				$this->onGround = true;
			}else{
				$this->onGround = false;
			}
		}
		$this->isCollided = $this->onGround;
		$this->updateFallState($dy, $this->onGround);

		Timings::$entityMoveTimer->stopTiming();

		return true;
	}

	protected function applyDragBeforeGravity() : bool{
		return false;
	}

	protected function applyGravity() : void{
		$this->motionY -= $this->gravity;
	}

	protected function tryChangeMovement() : void{
		$friction = 1 - $this->drag;

		if($this->applyDragBeforeGravity()){
			$this->motionY *= $friction;
		}

		$this->applyGravity();

		if(!$this->applyDragBeforeGravity()){
			$this->motionY *= $friction;
		}

		if($this->onGround){
			$friction *= $this->level->getBlockAt((int) floor($this->x), (int) floor($this->y - 1), (int) floor($this->z))->getFrictionFactor();
		}

		$this->motionX *= $friction;
		$this->motionZ *= $friction;
	}

	public function move($dx, $dy, $dz){
		$this->blocksAround = null;

		if($dx == 0 and $dz == 0 and $dy == 0){
			return true;
		}

		if($this->keepMovement){
			$this->boundingBox->offset($dx, $dy, $dz);
			$this->setPosition($this->temporalVector->setComponents(($this->boundingBox->minX + $this->boundingBox->maxX) / 2, $this->boundingBox->minY, ($this->boundingBox->minZ + $this->boundingBox->maxZ) / 2));
			$this->onGround = $this->isPlayer ? true : false;
			return true;
		}else{

			Timings::$entityMoveTimer->startTiming();

			$this->ySize *= self::STEP_CLIP_MULTIPLIER;

			/*
			if($this->isColliding){ //With cobweb?
				$this->isColliding = false;
				$dx *= 0.25;
				$dy *= 0.05;
				$dz *= 0.25;
				$this->motionX = 0;
				$this->motionY = 0;
				$this->motionZ = 0;
			}
			*/

			$movX = $dx;
			$movY = $dy;
			$movZ = $dz;

			$axisalignedbb = clone $this->boundingBox;

			/*$sneakFlag = $this->onGround and $this instanceof Player;

			if($sneakFlag){
				for($mov = 0.05; $dx != 0.0 and count($this->level->getCollisionCubes($this, $this->boundingBox->getOffsetBoundingBox($dx, -1, 0))) === 0; $movX = $dx){
					if($dx < $mov and $dx >= -$mov){
						$dx = 0;
					}elseif($dx > 0){
						$dx -= $mov;
					}else{
						$dx += $mov;
					}
				}

				for(; $dz != 0.0 and count($this->level->getCollisionCubes($this, $this->boundingBox->getOffsetBoundingBox(0, -1, $dz))) === 0; $movZ = $dz){
					if($dz < $mov and $dz >= -$mov){
						$dz = 0;
					}elseif($dz > 0){
						$dz -= $mov;
					}else{
						$dz += $mov;
					}
				}

				//TODO: big messy loop
			}*/

			assert(abs($dx) <= 20 and abs($dy) <= 20 and abs($dz) <= 20, "Movement distance is excessive: dx=$dx, dy=$dy, dz=$dz");

			$list = $this->level->getCollisionCubes($this, $this->level->getTickRate() > 1 ? $this->boundingBox->getOffsetBoundingBox($dx, $dy, $dz) : $this->boundingBox->addCoord($dx, $dy, $dz), false);

			foreach($list as $bb){
				$dy = $bb->calculateYOffset($this->boundingBox, $dy);
			}

			$this->boundingBox->offset(0, $dy, 0);

			$fallingFlag = ($this->onGround or ($dy != $movY and $movY < 0));

			foreach($list as $bb){
				$dx = $bb->calculateXOffset($this->boundingBox, $dx);
			}

			$this->boundingBox->offset($dx, 0, 0);

			foreach($list as $bb){
				$dz = $bb->calculateZOffset($this->boundingBox, $dz);
			}

			$this->boundingBox->offset(0, 0, $dz);


			if($this->stepHeight > 0 and $fallingFlag and ($movX != $dx or $movZ != $dz)){
				$cx = $dx;
				$cy = $dy;
				$cz = $dz;
				$dx = $movX;
				$dy = $this->stepHeight;
				$dz = $movZ;

				$axisalignedbb1 = clone $this->boundingBox;

				$this->boundingBox->setBB($axisalignedbb);

				$list = $this->level->getCollisionCubes($this, $this->boundingBox->addCoord($dx, $dy, $dz), false);

				foreach($list as $bb){
					$dy = $bb->calculateYOffset($this->boundingBox, $dy);
				}

				$this->boundingBox->offset(0, $dy, 0);

				foreach($list as $bb){
					$dx = $bb->calculateXOffset($this->boundingBox, $dx);
				}

				$this->boundingBox->offset($dx, 0, 0);

				foreach($list as $bb){
					$dz = $bb->calculateZOffset($this->boundingBox, $dz);
				}

				$this->boundingBox->offset(0, 0, $dz);

				$reverseDY = -$dy;
				foreach($list as $bb){
					$reverseDY = $bb->calculateYOffset($this->boundingBox, $reverseDY);
				}
				$dy += $reverseDY;
				$this->boundingBox->offset(0, $reverseDY, 0);

				if(($cx ** 2 + $cz ** 2) >= ($dx ** 2 + $dz ** 2)){
					$dx = $cx;
					$dy = $cy;
					$dz = $cz;
					$this->boundingBox->setBB($axisalignedbb1);
				}else{
					$this->ySize += $dy;
				}

			}

			$this->x = ($this->boundingBox->minX + $this->boundingBox->maxX) / 2;
			$this->y = $this->boundingBox->minY - $this->ySize;
			$this->z = ($this->boundingBox->minZ + $this->boundingBox->maxZ) / 2;

			$this->checkChunks();
			$this->checkBlockCollision();
			$this->checkGroundState($movX, $movY, $movZ, $dx, $dy, $dz);
			$this->updateFallState($dy, $this->onGround);

			if($movX != $dx){
				$this->motionX = 0;
			}

			if($movY != $dy){
				$this->motionY = 0;
			}

			if($movZ != $dz){
				$this->motionZ = 0;
			}


			//TODO: vehicle collision events (first we need to spawn them!)

			Timings::$entityMoveTimer->stopTiming();

			return true;
		}
	}

	protected function checkGroundState($movX, $movY, $movZ, $dx, $dy, $dz){
		$this->isCollidedVertically = $movY != $dy;
		$this->isCollidedHorizontally = ($movX != $dx or $movZ != $dz);
		$this->isCollided = ($this->isCollidedHorizontally or $this->isCollidedVertically);
		$this->onGround = ($movY != $dy and $movY < 0);
	}

	public function getBlocksAround(){
		if($this->blocksAround === null){
			$inset = 0.001; //Offset against floating-point errors

			$bb = $this->boundingBox->shrink($inset, $inset, $inset);

			$minX = (int) floor($bb->minX);
			$minY = (int) floor($bb->minY);
			$minZ = (int) floor($bb->minZ);
			$maxX = (int) ceil($bb->maxX);
			$maxY = (int) ceil($bb->maxY);
			$maxZ = (int) ceil($bb->maxZ);

			$this->blocksAround = [];

			for($z = $minZ; $z <= $maxZ; ++$z){
				for($x = $minX; $x <= $maxX; ++$x){
					for($y = $minY; $y <= $maxY; ++$y){
						$block = $this->level->getBlockAt($x, $y, $z);
						if($block->hasEntityCollision()){
							$this->blocksAround[] = $block;
						}
					}
				}
			}
		}

		return $this->blocksAround;
	}

	protected function checkBlockCollision(){
		$vector = new Vector3(0, 0, 0);

		foreach($this->getBlocksAround() as $block){
			$block->onEntityCollide($this);
			$block->addVelocityToEntity($this, $vector);
		}

		if($vector->lengthSquared() > 0){
			$vector = $vector->normalize();
			$d = 0.014;
			$this->motionX += $vector->x * $d;
			$this->motionY += $vector->y * $d;
			$this->motionZ += $vector->z * $d;
		}
	}

	public function setPositionAndRotation(Vector3 $pos, $yaw, $pitch){
		if($this->setPosition($pos) === true){
			$this->setRotation($yaw, $pitch);

			return true;
		}

		return false;
	}

	public function setRotation($yaw, $pitch){
		$this->yaw = $yaw;
		$this->pitch = $pitch;
		$this->scheduleUpdate();
	}

	public function broadcastMovement() : void{
		$pk = new MoveEntityPacket();
		$pk->entityRuntimeId = $this->id;
		[$pk->x, $pk->y, $pk->z] = [$this->x, $this->y + $this->baseOffset, $this->z];
		$pk->yaw = $this->yaw;
		$pk->pitch = $this->pitch;
		$pk->headYaw = $this->yaw; // TODO
		$pk->onGround = $this->onGround;
		$this->server->broadcastPacket($this->hasSpawned, $pk);

		if($this instanceof Player){
			$this->sendDataPacket($pk);
		}
	}

	public function broadcastMotion() : void{
		$pk = new SetEntityMotionPacket();
		$pk->entityRuntimeId = $this->id;
		[$pk->motionX, $pk->motionY, $pk->motionZ] = [$this->motionX, $this->motionY, $this->motionZ];
		$this->server->broadcastPacket($this->hasSpawned, $pk);

		if($this instanceof Player){
			$this->sendDataPacket($pk);
		}
	}

	protected function checkChunks(){
		if($this->chunk === null or ($this->chunk->getX() !== ($this->x >> 4) or $this->chunk->getZ() !== ($this->z >> 4))){
			if($this->chunk !== null){
				$this->chunk->removeEntity($this);
			}
			$this->chunk = $this->level->getChunk($this->x >> 4, $this->z >> 4, true);

			if(!$this->justCreated){
				$newChunk = $this->level->getChunkPlayers($this->x >> 4, $this->z >> 4);
				foreach($this->hasSpawned as $player){
					if(!isset($newChunk[$player->getLoaderId()])){
						$this->despawnFrom($player);
					}else{
						unset($newChunk[$player->getLoaderId()]);
					}
				}
				foreach($newChunk as $player){
					$this->spawnTo($player);
				}
			}

			if($this->chunk === null){
				return;
			}

			$this->chunk->addEntity($this);
		}
	}

	public function setPosition(Vector3 $pos){
		if($this->closed){
			return false;
		}

		if($pos instanceof Position and $pos->level !== null and $pos->level !== $this->level){
			if($this->switchLevel($pos->getLevel()) === false){
				return false;
			}
		}

		$this->x = $pos->x;
		$this->y = $pos->y;
		$this->z = $pos->z;

		$this->recalculateBoundingBox();

		$this->checkChunks();

		return true;
	}

	protected function resetLastMovements(){
		list($this->lastX, $this->lastY, $this->lastZ) = [$this->x, $this->y, $this->z];
		list($this->lastYaw, $this->lastPitch) = [$this->yaw, $this->pitch];
		list($this->lastMotionX, $this->lastMotionY, $this->lastMotionZ) = [$this->motionX, $this->motionY, $this->motionZ];
	}

	public function getMotion(){
		return new Vector3($this->motionX, $this->motionY, $this->motionZ);
	}

	public function getSpeed() : Vector3{
		return $this->getMotion();
	}

	public function setMotion(Vector3 $motion){
		if(!$this->justCreated){
			$ev = new EntityMotionEvent($this, $motion);
			$ev->call();
			if($ev->isCancelled()){
				return false;
			}
		}

		$this->motionX = $motion->x;
		$this->motionY = $motion->y;
		$this->motionZ = $motion->z;

		if(!$this->justCreated){
			$this->updateMovement();
		}

		return true;
	}

	public function isOnGround(){
		return $this->onGround === true;
	}

	public function kill(){
		$this->health = 0;
		$this->scheduleUpdate();
	}

	/**
	 * @param Vector3|Position|Location $pos
	 * @param float|null                $yaw
	 * @param float|null                $pitch
	 *
	 * @return bool
	 */
	public function teleport(Vector3 $pos, float $yaw = null, float $pitch = null) : bool{
		if($pos instanceof Location){
			$yaw = $yaw ?? $pos->yaw;
			$pitch = $pitch ?? $pos->pitch;
		}
		$from = Position::fromObject($this, $this->level);
		$to = Position::fromObject($pos, $pos instanceof Position ? $pos->getLevel() : $this->level);
		$ev = new EntityTeleportEvent($this, $from, $to);
		$ev->call();
		if($ev->isCancelled()){
			return false;
		}
		$this->ySize = 0;
		$pos = $ev->getTo();

		$this->setMotion($this->temporalVector->setComponents(0, 0, 0));
		if($this->setPositionAndRotation($pos, $yaw ?? $this->yaw, $pitch ?? $this->pitch) !== false){
			$this->resetFallDistance();
			$this->onGround = true;

			$this->lastX = $this->x;
			$this->lastY = $this->y;
			$this->lastZ = $this->z;

			$this->lastYaw = $this->yaw;
			$this->lastPitch = $this->pitch;

			$this->updateMovement();

			return true;
		}

		return false;
	}

	public function getId(){
		return $this->id;
	}

	public function respawnToAll(){
		foreach($this->hasSpawned as $key => $player){
			unset($this->hasSpawned[$key]);
			$this->spawnTo($player);
		}
	}

	public function spawnToAll(){
		if($this->chunk === null or $this->closed){
			return;
		}
		foreach($this->level->getViewersForPosition($this) as $player){
			if($player->isOnline()){
				$this->spawnTo($player);
			}
		}
	}

	public function despawnFromAll(){
		foreach($this->hasSpawned as $player){
			$this->despawnFrom($player);
		}
	}

	public function broadcastEntityEvent(int $eventId, int $eventData = null, array $players = null) : void{
		$pk = new EntityEventPacket();
		$pk->entityRuntimeId = $this->id;
		$pk->event = $eventId;
		$pk->data = $eventData ?? 0;
		$this->server->broadcastPacket($players ?? $this->getViewers(), $pk);
	}

	/**
	 * Flags the entity to be removed from the world on the next tick.
	 */
	public function flagForDespawn() : void{
		$this->needsDespawn = true;
		$this->scheduleUpdate();
	}

	public function isFlaggedForDespawn() : bool{
		return $this->needsDespawn;
	}

	public function close(){
		if(!$this->closed){
			(new EntityDespawnEvent($this))->call();
			$this->closed = true;

			$this->despawnFromAll();
			$this->hasSpawned = [];

			if($this->chunk !== null){
				$this->chunk->removeEntity($this);
				$this->chunk = null;
			}

			if($this->getLevel() !== null){
				$this->getLevel()->removeEntity($this);
				$this->setLevel(null);
			}

			$this->namedtag = null;
			$this->lastDamageCause = null;
		}
	}

	/**
	 * @param int   $id
	 * @param int   $type
	 * @param mixed $value
	 * @param bool  $send
	 *
	 * @return bool
	 */
	public function setDataProperty($id, $type, $value, bool $send = true){
		if($this->getDataProperty($id) !== $value){
			$this->dataProperties[$id] = [$type, $value];
			if($send){
				$this->changedDataProperties[$id] = $this->dataProperties[$id]; //This will be sent on the next tick
			}

			return true;
		}

		return false;
	}

	/**
	 * @param int $id
	 *
	 * @return mixed
	 */
	public function getDataProperty($id){
		return isset($this->dataProperties[$id]) ? $this->dataProperties[$id][1] : null;
	}

	/**
	 * @return array[]
	 */
	public function getDataProperties(){
		return $this->dataProperties;
	}

	/**
	 * @param int $id
	 *
	 * @return int|null
	 */
	public function getDataPropertyType($id){
		return isset($this->dataProperties[$id]) ? $this->dataProperties[$id][0] : null;
	}

	/**
	 * @param int  $propertyId
	 * @param int  $id
	 * @param bool $value
	 * @param int  $type
	 */
	public function setDataFlag($propertyId, $id, $value = true, $type = self::DATA_TYPE_LONG){
		if($this->getDataFlag($propertyId, $id) !== $value){
			$flags = (int) $this->getDataProperty($propertyId);
			$flags ^= 1 << $id;
			$this->setDataProperty($propertyId, $type, $flags);
		}
	}

	/**
	 * @param int $propertyId
	 * @param int $id
	 *
	 * @return bool
	 */
	public function getDataFlag($propertyId, $id){
		return (((int) $this->getDataProperty($propertyId)) & (1 << $id)) > 0;
	}

	public function __destruct(){
		$this->close();
	}


	public function setMetadata(string $metadataKey, MetadataValue $newMetadataValue){
		$this->server->getEntityMetadata()->setMetadata($this, $metadataKey, $newMetadataValue);
	}

	public function getMetadata(string $metadataKey){
		return $this->server->getEntityMetadata()->getMetadata($this, $metadataKey);
	}

	public function hasMetadata(string $metadataKey) : bool{
		return $this->server->getEntityMetadata()->hasMetadata($this, $metadataKey);
	}

	public function removeMetadata(string $metadataKey, Plugin $owningPlugin){
		$this->server->getEntityMetadata()->removeMetadata($this, $metadataKey, $owningPlugin);
	}

	public function __toString(){
		return (new \ReflectionClass($this))->getShortName() . "(" . $this->getId() . ")";
	}

}
