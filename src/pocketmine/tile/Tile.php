<?php

declare(strict_types=1);

/**
 * All the Tile classes and related classes
 */
namespace pocketmine\tile;

use pocketmine\block\Block;
use pocketmine\event\Timings;
use pocketmine\event\TimingsHandler;
use pocketmine\level\format\Chunk;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\nbt\tag\CompoundTag;

use function assert;
use function is_a;
use function microtime;

abstract class Tile extends Position{

	public const BREWING_STAND = "BrewingStand";
	public const CHEST = "Chest";
	public const ENCHANT_TABLE = "EnchantTable";
	public const ENDER_CHEST = "EnderChest";
	public const FLOWER_POT = "FlowerPot";
	public const FURNACE = "Furnace";
	public const ITEM_FRAME = "ItemFrame";
	public const MOB_SPAWNER = "MobSpawner";
	public const SIGN = "Sign";
	public const SKULL = "Skull";
	public const BED = "Bed";
	public const HOPPER = "Hopper";
	public const DROPPER = "Dropper";

	public static $tileCount = 1;

	private static $knownTiles = [];
	private static $shortNames = [];

	/** @var Chunk */
	public $chunk;
	public $name;
	public $id;
	public $attach;
	public $metadata;
	public $closed = false;
	public $namedtag;
	protected $lastUpdate;
	protected $server;
	protected $timings;

	/** @var TimingsHandler */
	public $tickTimer;

	public static function init(){
		self::registerTile(Bed::class);
		self::registerTile(Chest::class);
		self::registerTile(EnchantTable::class);
		self::registerTile(EnderChest::class);
		self::registerTile(FlowerPot::class);
		self::registerTile(Furnace::class);
		self::registerTile(ItemFrame::class);
		self::registerTile(Sign::class);
		self::registerTile(Skull::class);
	}

	/**
	 * @param string      $type
	 * @param Level       $level
	 * @param CompoundTag $nbt
	 * @param             $args
	 *
	 * @return Tile|null
	 */
	public static function createTile($type, Level $level, CompoundTag $nbt, ...$args){
		if(isset(self::$knownTiles[$type])){
			$class = self::$knownTiles[$type];
			return new $class($level, $nbt, ...$args);
		}

		return null;
	}

	/**
	 * @param $className
	 *
	 * @return bool
	 */
	public static function registerTile($className) : bool{
		$class = new \ReflectionClass($className);
		if(is_a($className, Tile::class, true) and !$class->isAbstract()){
			self::$knownTiles[$class->getShortName()] = $className;
			self::$shortNames[$className] = $class->getShortName();
			return true;
		}

		return false;
	}

	/**
	 * Returns the short save name
	 * @return string
	 */
	public function getSaveId() : string{
		return self::$shortNames[static::class];
	}

	public function __construct(Level $level, CompoundTag $nbt){
		$this->timings = Timings::getTileEntityTimings($this);

		$this->namedtag = $nbt;

		$this->x = $this->namedtag->getInt("x");
		$this->y = $this->namedtag->getInt("y");
		$this->z = $this->namedtag->getInt("z");

		$this->server = $level->getServer();
		$this->setLevel($level);
		$this->chunk = $level->getChunk($this->x >> 4, $this->z >> 4, false);
		assert($this->chunk !== null);

		$this->name = "";
		$this->lastUpdate = microtime(true);
		$this->id = Tile::$tileCount++;

		$this->getLevel()->addTile($this);
		$this->tickTimer = Timings::getTileEntityTimings($this);
	}

	public function getId(){
		return $this->id;
	}

	public function saveNBT(){
		$this->namedtag->setString("id", $this->getSaveId());
		$this->namedtag->setInt("x", $this->x);
		$this->namedtag->setInt("y", $this->y);
		$this->namedtag->setInt("z", $this->z);
	}

	public function getCleanedNBT(){
		$this->saveNBT();
		$tag = clone $this->namedtag;
		$tag->removeTag("x", "y", "z");
		if($tag->getCount() > 0){
			return $tag;
		}else{
			return null;
		}
	}

	/**
	 * @return Block
	 */
	public function getBlock() : Block{
		return $this->level->getBlockAt($this->x, $this->y, $this->z);
	}

	/**
	 * @return bool
	 */
	public function onUpdate() : bool{
		return false;
	}

	final public function scheduleUpdate(){
		$this->level->updateTiles[$this->id] = $this;
	}

	public function __destruct(){
		$this->close();
	}

	public function close(){
		if(!$this->closed){
			$this->closed = true;
			unset($this->level->updateTiles[$this->id]);
			if(($level = $this->getLevel()) instanceof Level){
				$level->removeTile($this);
				$this->setLevel(null);
			}
			$this->chunk = null;
		}
	}

	public function getName(){
		return $this->name;
	}

}
