<?php

declare(strict_types=1);

/**
 * PocketMine-MP is the Minecraft: PE multiplayer server software
 * Homepage: http://www.pocketmine.net/
 */
namespace pocketmine;

use pocketmine\block\Block;
use pocketmine\command\CommandReader;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\command\SimpleCommandMap;
use pocketmine\entity\Attribute;
use pocketmine\entity\Effect;
use pocketmine\entity\Entity;
use pocketmine\event\HandlerList;
use pocketmine\event\level\LevelInitEvent;
use pocketmine\event\level\LevelLoadEvent;
use pocketmine\event\player\PlayerDataSaveEvent;
use pocketmine\event\server\QueryRegenerateEvent;
use pocketmine\event\server\ServerCommandEvent;
use pocketmine\event\TextContainer;
use pocketmine\event\Timings;
use pocketmine\event\TimingsHandler;
use pocketmine\event\TranslationContainer;
use pocketmine\inventory\CraftingManager;
use pocketmine\inventory\InventoryType;
use pocketmine\inventory\Recipe;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item;
use pocketmine\lang\BaseLang;
use pocketmine\level\format\io\leveldb\LevelDB;
use pocketmine\level\format\io\LevelProvider;
use pocketmine\level\format\io\LevelProviderManager;
use pocketmine\level\format\io\region\Anvil;
use pocketmine\level\format\io\region\McRegion;
use pocketmine\level\format\io\region\PMAnvil;
use pocketmine\level\generator\biome\Biome;
use pocketmine\level\generator\Flat;
use pocketmine\level\generator\Generator;
use pocketmine\level\generator\hell\Nether;
use pocketmine\level\generator\normal\Normal;
use pocketmine\level\generator\VoidGenerator;
use pocketmine\level\Level;
use pocketmine\level\LevelException;
use pocketmine\metadata\EntityMetadataStore;
use pocketmine\metadata\LevelMetadataStore;
use pocketmine\metadata\PlayerMetadataStore;
use pocketmine\nbt\BigEndianNbtSerializer;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\TreeRoot;
use pocketmine\network\bedrock\adapter\ProtocolAdapterFactory;
use pocketmine\network\bedrock\CompressBatchTask as BedrockCompressBatchedTask;
use pocketmine\network\bedrock\NetworkCompression as BedrockNetworkCompression;
use pocketmine\network\bedrock\PacketTranslator;
use pocketmine\network\bedrock\protocol\DataPacket as BedrockPacket;
use pocketmine\network\bedrock\protocol\ProtocolInfo as BedrockProtocolInfo;
use pocketmine\network\CompressBatchPromise;
use pocketmine\network\mcpe\CompressBatchTask as McpeCompressBatchedTask;
use pocketmine\network\mcpe\encryption\EncryptionContext;
use pocketmine\network\mcpe\NetworkCompression as McpeNetworkCompression;
use pocketmine\network\mcpe\protocol\BatchPacket;
use pocketmine\network\mcpe\protocol\DataPacket;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\network\mcpe\RakLibInterface;
use pocketmine\network\Network;
use pocketmine\network\query\QueryHandler;
use pocketmine\network\rcon\RCON;
use pocketmine\network\upnp\UPnP;
use pocketmine\permission\BanList;
use pocketmine\permission\DefaultPermissions;
use pocketmine\plugin\PharPluginLoader;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginLoadOrder;
use pocketmine\plugin\PluginManager;
use pocketmine\plugin\ScriptPluginLoader;
use pocketmine\resourcepacks\ResourcePackManager;
use pocketmine\scheduler\FileWriteTask;
use pocketmine\scheduler\ServerScheduler;
use pocketmine\snooze\SleeperHandler;
use pocketmine\snooze\SleeperNotifier;
use pocketmine\tile\Tile;
use pocketmine\utils\Binary;
use pocketmine\utils\Config;
use pocketmine\utils\Internet;
use pocketmine\utils\MainLogger;
use pocketmine\utils\ServerKiller;
use pocketmine\utils\Terminal;
use pocketmine\utils\TextFormat;
use pocketmine\utils\Utils;
use pocketmine\utils\UUID;
use pocketmine\utils\VersionString;
use pocketmine\utils\Zlib;
use RuntimeException;
use function array_key_exists;
use function array_shift;
use function array_sum;
use function asort;
use function assert;
use function base64_encode;
use function bccomp;
use function class_exists;
use function cli_set_process_title;
use function count;
use function define;
use function explode;
use function extension_loaded;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function floor;
use function function_exists;
use function getmypid;
use function getopt;
use function implode;
use function ini_get;
use function ini_set;
use function is_array;
use function is_bool;
use function is_numeric;
use function is_string;
use function is_subclass_of;
use function json_decode;
use function max;
use function microtime;
use function min;
use function mkdir;
use function pcntl_signal;
use function pcntl_signal_dispatch;
use function preg_replace;
use function random_bytes;
use function realpath;
use function register_shutdown_function;
use function rename;
use function round;
use function spl_object_id;
use function sprintf;
use function str_repeat;
use function str_replace;
use function stripos;
use function strlen;
use function strrpos;
use function strtolower;
use function substr;
use function time;
use function touch;
use function trim;
use function usleep;
use const DIRECTORY_SEPARATOR;
use const E_ERROR;
use const E_USER_ERROR;
use const E_USER_WARNING;
use const E_WARNING;
use const PHP_INT_MAX;
use const PHP_INT_SIZE;
use const PTHREADS_INHERIT_NONE;
use const SIGHUP;
use const SIGINT;
use const SIGTERM;

/**
 * The class that manages everything
 */
class Server{
	public const BROADCAST_CHANNEL_ADMINISTRATIVE = "pocketmine.broadcast.admin";
	public const BROADCAST_CHANNEL_USERS = "pocketmine.broadcast.user";

	/** @var Server */
	private static $instance = null;

	/** @var \Threaded */
	private static $sleeper = null;

	/** @var SleeperHandler */
	private $tickSleeper;

	/** @var BanList */
	private $banByName = null;

	/** @var BanList */
	private $banByIP = null;

	/** @var Config */
	private $operators = null;

	/** @var Config */
	private $whitelist = null;

	/** @var bool */
	private $isRunning = true;

	private $hasStopped = false;

	/** @var PluginManager */
	private $pluginManager = null;

	private $profilingTickRate = 20;

	/** @var ServerScheduler */
	private $scheduler = null;

	/**
	 * Counts the ticks since the server start
	 *
	 * @var int
	 */
	private $tickCounter = 0;
	private $nextTick = 0;
	private $tickAverage = [20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20];
	private $useAverage = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
	private $currentTPS = 20;
	private $currentUse = 0;

	/** @var bool */
	private $doTitleTick = true;

	private $dispatchSignals = false;

	/** @var \AttachableThreadedLogger */
	private $logger;

	/** @var MemoryManager */
	private $memoryManager;

	/** @var CommandReader */
	private $console = null;

	/** @var SimpleCommandMap */
	private $commandMap = null;

	/** @var CraftingManager */
	private $craftingManager;

	/** @var ResourcePackManager */
	private $pw10ResourcePackManager;
	/** @var ResourcePackManager */
	private $bedrockResourcePackManager;

	/** @var ConsoleCommandSender */
	private $consoleSender;

	/** @var int */
	private $maxPlayers;

	/** @var bool */
	private $autoSave;

	/** @var RCON */
	private $rcon;

	/** @var EntityMetadataStore */
	private $entityMetadata;

	/** @var PlayerMetadataStore */
	private $playerMetadata;

	/** @var LevelMetadataStore */
	private $levelMetadata;

	/** @var Network */
	private $network;

	private $networkCompressionAsync = true;
	public $networkCompressionLevel = 7;

	private $autoTickRate = true;
	private $autoTickRateLimit = 20;
	private $alwaysTickPlayers = false;
	private $baseTickRate = 1;

	private $autoSaveTicker = 0;
	private $autoSaveTicks = 6000;

	/** @var BaseLang */
	private $baseLang;

	private $forceLanguage = false;

	private $serverID;

	private $autoloader;
	private $filePath;
	private $dataPath;
	private $pluginPath;

	/** @var QueryHandler */
	private $queryHandler;

	/** @var QueryRegenerateEvent */
	private $queryRegenerateTask = null;

	/** @var Config */
	private $properties;

	private $propertyCache = [];

	/** @var Config */
	private $config;

	/** @var Config */
	private $advancedConfig;
	
	/** @var int */
	private $port;

	/** @var Player[] */
	private $players = [];

	/** @var Player[] */
	private $playerList = [];

	/** @var string[] */
	private $identifiers = [];

	/** @var Level[] */
	private $levels = [];

	/** @var Level */
	private $levelDefault = null;

	public $allowInventoryCheats = false;

	/**
	 * @return string
	 */
	public function getName() : string{
		return \pocketmine\NAME;
	}

	/**
	 * @return bool
	 */
	public function isRunning() : bool{
		return $this->isRunning === true;
	}

	/**
	 * @return string
	 */
	public function getPocketMineVersion() : string{
		return \pocketmine\VERSION;
	}

	/**
	 * @return string
	 */
	public function getCodename() : string{
		return \pocketmine\CODENAME;
	}

	/**
	 * @return string
	 */
	public function getVersion() : string{
		return ProtocolInfo::MINECRAFT_VERSION;
	}

	/**
	 * @return string
	 */
	public function getBedrockVersion() : string{
		return BedrockProtocolInfo::MINECRAFT_VERSION;
	}

	/**
	 * @return string
	 */
	public function getApiVersion() : string{
		return \pocketmine\API_VERSION;
	}

	/**
	 * @return string
	 */
	public function getFilePath() : string{
		return $this->filePath;
	}

	/**
	 * @return string
	 */
	public function getDataPath() : string{
		return $this->dataPath;
	}

	/**
	 * @return string
	 */
	public function getPluginPath() : string{
		return $this->pluginPath;
	}

	/**
	 * @return int
	 */
	public function getMaxPlayers() : int{
		return $this->maxPlayers;
	}

	/**
	 * @return int
	 */
	public function getPort() : int{
		return $this->port;
	}

	/**
	 * @return int
	 */
	public function getViewDistance() : int{
		return max(2, $this->getConfigInt("view-distance", 8));
	}

	/**
	 * Returns a view distance up to the currently-allowed limit.
	 *
	 * @param int $distance
	 *
	 * @return int
	 */
	public function getAllowedViewDistance(int $distance) : int{
		return max(2, min($distance, $this->memoryManager->getViewDistance($this->getViewDistance())));
	}

	/**
	 * @return string
	 */
	public function getIp() : string{
		return $this->getConfigString("server-ip", "0.0.0.0");
	}

	/**
	 * @return UUID
	 */
	public function getServerUniqueId(){
		return $this->serverID;
	}

	/**
	 * @return bool
	 */
	public function getAutoSave() : bool{
		return $this->autoSave;
	}

	/**
	 * @param bool $value
	 */
	public function setAutoSave(bool $value){
		$this->autoSave = $value;
		foreach($this->getLevels() as $level){
			$level->setAutoSave($this->autoSave);
		}
	}

	/**
	 * @return string
	 */
	public function getLevelType() : string{
		return $this->getConfigString("level-type", "DEFAULT");
	}

	/**
	 * @return bool
	 */
	public function getGenerateStructures() : bool{
		return $this->getConfigBoolean("generate-structures", true);
	}

	/**
	 * @return int
	 */
	public function getGamemode() : int{
		return $this->getConfigInt("gamemode", 0) & 0b11;
	}

	/**
	 * @return bool
	 */
	public function getForceGamemode() : bool{
		return $this->getConfigBoolean("force-gamemode", false);
	}

	/**
	 * Returns the gamemode text name
	 *
	 * @param int $mode
	 *
	 * @return string
	 */
	public static function getGamemodeString(int $mode) : string{
		switch((int) $mode){
			case Player::SURVIVAL:
				return "%gameMode.survival";
			case Player::CREATIVE:
				return "%gameMode.creative";
			case Player::ADVENTURE:
				return "%gameMode.adventure";
			case Player::SPECTATOR:
				return "%gameMode.spectator";
		}

		return "UNKNOWN";
	}

	public static function getGamemodeName(int $mode) : string{
		switch($mode){
			case Player::SURVIVAL:
				return "Survival";
			case Player::CREATIVE:
				return "Creative";
			case Player::ADVENTURE:
				return "Adventure";
			case Player::SPECTATOR:
				return "Spectator";
			default:
				throw new \InvalidArgumentException("Invalid gamemode $mode");
		}
	}

	/**
	 * Parses a string and returns a gamemode integer, -1 if not found
	 *
	 * @param string $str
	 *
	 * @return int
	 */
	public static function getGamemodeFromString(string $str) : int{
		switch(strtolower(trim($str))){
			case (string) Player::SURVIVAL:
			case "survival":
			case "s":
				return Player::SURVIVAL;

			case (string) Player::CREATIVE:
			case "creative":
			case "c":
				return Player::CREATIVE;

			case (string) Player::ADVENTURE:
			case "adventure":
			case "a":
				return Player::ADVENTURE;

			case (string) Player::SPECTATOR:
			case "spectator":
			case "view":
			case "v":
				return Player::SPECTATOR;
		}
		return -1;
	}

	/**
	 * @param string $str
	 *
	 * @return int
	 */
	public static function getDifficultyFromString(string $str) : int{
		switch(strtolower(trim($str))){
			case "0":
			case "peaceful":
			case "p":
				return 0;

			case "1":
			case "easy":
			case "e":
				return 1;

			case "2":
			case "normal":
			case "n":
				return 2;

			case "3":
			case "hard":
			case "h":
				return 3;
		}
		return -1;
	}

	/**
	 * @return int
	 */
	public function getDifficulty() : int{
		return $this->getConfigInt("difficulty", 1);
	}

	/**
	 * @return bool
	 */
	public function hasWhitelist() : bool{
		return $this->getConfigBoolean("white-list", false);
	}

	/**
	 * @return bool
	 */
	public function getAllowFlight() : bool{
		return $this->getConfigBoolean("allow-flight", false);
	}

	/**
	 * @return bool
	 */
	public function isHardcore() : bool{
		return $this->getConfigBoolean("hardcore", false);
	}

	/**
	 * @return int
	 */
	public function getDefaultGamemode() : int{
		return $this->getConfigInt("gamemode", 0) & 0b11;
	}

	/**
	 * @return string
	 */
	public function getMotd() : string{
		return $this->getConfigString("motd", "Minecraft: PE Server");
	}

	/**
	 * @return \ClassLoader
	 */
	public function getLoader(){
		return $this->autoloader;
	}

	/**
	 * @return MainLogger
	 */
	public function getLogger(){
		return $this->logger;
	}

	/**
	 * @return EntityMetadataStore
	 */
	public function getEntityMetadata(){
		return $this->entityMetadata;
	}

	/**
	 * @return PlayerMetadataStore
	 */
	public function getPlayerMetadata(){
		return $this->playerMetadata;
	}

	/**
	 * @return LevelMetadataStore
	 */
	public function getLevelMetadata(){
		return $this->levelMetadata;
	}

	/**
	 * @return PluginManager
	 */
	public function getPluginManager(){
		return $this->pluginManager;
	}

	/**
	 * @return CraftingManager
	 */
	public function getCraftingManager(){
		return $this->craftingManager;
	}

	/**
	 * @return ResourcePackManager
	 */
	public function getPw10ResourcePackManager() : ResourcePackManager{
		return $this->pw10ResourcePackManager;
	}

	/**
	 * @return ResourcePackManager
	 */
	public function getBedrockResourcePackManager() : ResourcePackManager{
		return $this->bedrockResourcePackManager;
	}

	/**
	 * @return ServerScheduler
	 */
	public function getScheduler(){
		return $this->scheduler;
	}

	/**
	 * @return int
	 */
	public function getTick() : int{
		return $this->tickCounter;
	}

	/**
	 * Returns the last server TPS measure
	 *
	 * @return float
	 */
	public function getTicksPerSecond() : float{
		return round($this->currentTPS, 2);
	}

	/**
	 * Returns the last server TPS average measure
	 *
	 * @return float
	 */
	public function getTicksPerSecondAverage() : float{
		return round(array_sum($this->tickAverage) / count($this->tickAverage), 2);
	}

	/**
	 * Returns the TPS usage/load in %
	 *
	 * @return float
	 */
	public function getTickUsage() : float{
		return round($this->currentUse * 100, 2);
	}

	/**
	 * Returns the TPS usage/load average in %
	 *
	 * @return float
	 */
	public function getTickUsageAverage() : float{
		return round((array_sum($this->useAverage) / count($this->useAverage)) * 100, 2);
	}

	/**
	 * @return SimpleCommandMap
	 */
	public function getCommandMap(){
		return $this->commandMap;
	}

	/**
	 * @return Player[]
	 */
	public function getOnlinePlayers() : array{
		return $this->playerList;
	}
	
	/**
	 * @return Player[]
	 */
	public function getPlayers() : array{
		return $this->players;
	}

	public function addRecipe(Recipe $recipe){
		$this->craftingManager->registerRecipe($recipe);
	}

	public function shouldSavePlayerData() : bool{
		return (bool) $this->getProperty("player.save-player-data", true);
	}

	/**
	 * @param string $name
	 *
	 * @return OfflinePlayer|Player
	 */
	public function getOfflinePlayer(string $name){
		$name = strtolower($name);
		$result = $this->getPlayerExact($name);

		if($result === null){
			$result = new OfflinePlayer($this, $name);
		}

		return $result;
	}

	/**
	 * @param string $name
	 *
	 * @return CompoundTag
	 */
	public function getOfflinePlayerData(string $name) : CompoundTag{
		$name = strtolower($name);
		$path = $this->getDataPath() . "players/";
		if($this->shouldSavePlayerData()){
			if(file_exists($path . "$name.dat")){
				try{
					$contents = @file_get_contents($path . "$name.dat");
					if($contents === false){
						throw new RuntimeException("Failed to read player data file \"$path\" (permission denied?)");
					}

					$decompressed = @Zlib::decompress($contents);
					if($decompressed === false){
						throw new RuntimeException("Failed to decompress raw player data for \"$name\"");
					}

					return (new BigEndianNbtSerializer())->read($decompressed)->mustGetCompoundTag();
				}catch(\Throwable $e){ //zlib decode error / corrupt data
					rename($path . "$name.dat", $path . "$name.dat.bak");
					$this->logger->notice($this->getLanguage()->translateString("pocketmine.data.playerCorrupted", [$name]));
				}
			}else{
				$this->logger->notice($this->getLanguage()->translateString("pocketmine.data.playerNotFound", [$name]));
			}
		}
		$spawn = $this->getDefaultLevel()->getSafeSpawn();
		return CompoundTag::create()
			->setLong("firstPlayed", (int) (microtime(true) * 1000))
			->setLong("lastPlayed", (int) (microtime(true) * 1000))
			->setTag("Pos", new ListTag([
				new DoubleTag($spawn->x),
				new DoubleTag($spawn->y),
				new DoubleTag($spawn->z)
			]))
			->setString("Level", $this->getDefaultLevel()->getName())
			//->setString("SpawnLevel", $this->getDefaultLevel()->getName())
			//->setInt("SpawnX", (int) $spawn->x)
			//->setInt("SpawnY", (int) $spawn->y)
			//->setInt("SpawnZ", (int) $spawn->z)
			//->setByte("SpawnForced", 1) //TODO
			->setTag("Inventory", new ListTag([], NBT::TAG_Compound))
			->setTag("EnderChestInventory", new ListTag([], NBT::TAG_Compound))
			->setInt("playerGameType", $this->getGamemode())
			->setTag("Motion", new ListTag([
				new DoubleTag( 0.0),
				new DoubleTag(0.0),
				new DoubleTag(0.0)
			]))
			->setTag("Rotation", new ListTag([
				new FloatTag(0.0),
				new FloatTag(0.0)
			]))
			->setFloat("FallDistance", 0.0)
			->setShort("Fire", 0)
			->setShort("Air", 300)
			->setByte("OnGround", 1)
			->setByte("Invulnerable", 0)
			->setString("NameTag", $name);
	}

	/**
	 * @param string      $name
	 * @param CompoundTag $nbtTag
	 * @param bool        $async
	 */
	public function saveOfflinePlayerData(string $name, CompoundTag $nbtTag, bool $async = false){
		$ev = new PlayerDataSaveEvent($nbtTag, $name);
		$ev->setCancelled(!$this->shouldSavePlayerData());

		$ev->call();

		if(!$ev->isCancelled()){
			$nbt = new BigEndianNbtSerializer();
			try{
				$data = Zlib::compress($nbt->write(new TreeRoot($ev->getSaveData())), ZLIB_ENCODING_GZIP);

				if($async){
					$this->getScheduler()->scheduleAsyncTask(new FileWriteTask($this->getDataPath() . "players/" . strtolower($name) . ".dat", $data));
				}else{
					file_put_contents($this->getDataPath() . "players/" . strtolower($name) . ".dat", $data);
				}
			}catch(\Throwable $e){
				$this->logger->critical($this->getLanguage()->translateString("pocketmine.data.saveError", [$name, $e->getMessage()]));
				$this->logger->logException($e);
			}
		}
	}

	/**
	 * @param string $name
	 *
	 * @return Player|null
	 */
	public function getPlayer(string $name){
		$found = null;
		$name = strtolower($name);
		$delta = PHP_INT_MAX;
		foreach($this->getOnlinePlayers() as $player){
			if(stripos($player->getName(), $name) === 0){
				$curDelta = strlen($player->getName()) - strlen($name);
				if($curDelta < $delta){
					$found = $player;
					$delta = $curDelta;
				}
				if($curDelta === 0){
					break;
				}
			}
		}

		return $found;
	}

	/**
	 * @param string $name
	 *
	 * @return Player|null
	 */
	public function getPlayerExact(string $name){
		$name = strtolower($name);
		foreach($this->getOnlinePlayers() as $player){
			if($player->getLowerCaseName() === $name){
				return $player;
			}
		}

		return null;
	}

	/**
	 * @param string $partialName
	 *
	 * @return Player[]
	 */
	public function matchPlayer(string $partialName) : array{
		$partialName = strtolower($partialName);
		$matchedPlayers = [];
		foreach($this->getOnlinePlayers() as $player){
			if($player->getLowerCaseName() === $partialName){
				$matchedPlayers = [$player];
				break;
			}elseif(stripos($player->getName(), $partialName) !== false){
				$matchedPlayers[] = $player;
			}
		}

		return $matchedPlayers;
	}

	/**
	 * @param Player $player
	 */
	public function removePlayer(Player $player){
		if(isset($this->identifiers[$hash = spl_object_id($player)])){
			$identifier = $this->identifiers[$hash];
			unset($this->players[$identifier]);
			unset($this->identifiers[$hash]);
			return;
		}

		foreach($this->players as $identifier => $p){
			if($player === $p){
				unset($this->players[$identifier]);
				unset($this->identifiers[spl_object_id($player)]);
				break;
			}
		}
	}

	/**
	 * @return Level[]
	 */
	public function getLevels() : array{
		return $this->levels;
	}

	/**
	 * @return Level|null
	 */
	public function getDefaultLevel(){
		return $this->levelDefault;
	}

	/**
	 * Sets the default level to a different level
	 * This won't change the level-name property,
	 * it only affects the server on runtime
	 *
	 * @param Level|null $level
	 */
	public function setDefaultLevel($level){
		if($level === null or ($this->isLevelLoaded($level->getFolderName()) and $level !== $this->levelDefault)){
			$this->levelDefault = $level;
		}
	}

	/**
	 * @param string $name
	 *
	 * @return bool
	 */
	public function isLevelLoaded(string $name) : bool{
		return $this->getLevelByName($name) instanceof Level;
	}

	/**
	 * @param int $levelId
	 *
	 * @return Level|null
	 */
	public function getLevel(int $levelId){
		if(isset($this->levels[$levelId])){
			return $this->levels[$levelId];
		}

		return null;
	}

	/**
	 * NOTE: This matches levels based on the FOLDER name, NOT the display name.
	 *
	 * @param string $name
	 *
	 * @return Level|null
	 */
	public function getLevelByName(string $name){
		foreach($this->getLevels() as $level){
			if($level->getFolderName() === $name){
				return $level;
			}
		}

		return null;
	}

	/**
	 * @param Level $level
	 * @param bool  $forceUnload
	 *
	 * @return bool
	 *
	 * @throws \InvalidStateException
	 */
	public function unloadLevel(Level $level, bool $forceUnload = false) : bool{
		if($level === $this->getDefaultLevel() and !$forceUnload){
			throw new \InvalidStateException("The default level cannot be unloaded while running, please switch levels.");
		}
		if($level->unload($forceUnload) === true){
			unset($this->levels[$level->getId()]);

			return true;
		}

		return false;
	}

	/**
	 * Loads a level from the data directory
	 *
	 * @param string $name
	 *
	 * @return bool
	 *
	 * @throws LevelException
	 */
	public function loadLevel(string $name) : bool{
		if(trim($name) === ""){
			throw new LevelException("Invalid empty level name");
		}
		if($this->isLevelLoaded($name)){
			return true;
		}elseif(!$this->isLevelGenerated($name)){
			$this->logger->notice($this->getLanguage()->translateString("pocketmine.level.notFound", [$name]));

			return false;
		}

		$path = $this->getDataPath() . "worlds/" . $name . "/";

		$provider = LevelProviderManager::getProvider($path);

		if($provider === null){
			$this->logger->error($this->getLanguage()->translateString("pocketmine.level.loadError", [$name, "Unknown provider"]));

			return false;
		}

		try{
			$level = new Level($this, $name, $path, $provider);
		}catch(\Throwable $e){

			$this->logger->error($this->getLanguage()->translateString("pocketmine.level.loadError", [$name, $e->getMessage()]));
			$this->logger->logException($e);
			return false;
		}

		$this->levels[$level->getId()] = $level;

		(new LevelLoadEvent($level))->call();

		$level->setTickRate($this->baseTickRate);

		return true;
	}

	/**
	 * Generates a new level if it does not exists
	 *
	 * @param string      $name
	 * @param int|null    $seed
	 * @param string|null $generator Class name that extends pocketmine\level\generator\Noise
	 * @param array       $options
	 *
	 * @return bool
	 */
	public function generateLevel(string $name, int $seed = null, $generator = null, array $options = []) : bool{
		if(trim($name) === "" or $this->isLevelGenerated($name)){
			return false;
		}

		$seed = $seed ?? Binary::readInt(random_bytes(4));

		if(!isset($options["preset"])){
			$options["preset"] = $this->getConfigString("generator-settings", "");
		}

		if(!($generator !== null and class_exists($generator, true) and is_subclass_of($generator, Generator::class))){
			$generator = Generator::getGenerator($this->getLevelType());
		}

		if(($provider = LevelProviderManager::getProviderByName($providerName = $this->getProperty("level-settings.default-format", "pmanvil"))) === null){
			$provider = LevelProviderManager::getProviderByName($providerName = "pmanvil");
		}

		try{
			$path = $this->getDataPath() . "worlds/" . $name . "/";
			/** @var LevelProvider $provider */
			$provider::generate($path, $name, $seed, $generator, $options);

			$level = new Level($this, $name, $path, (string) $provider);
			$this->levels[$level->getId()] = $level;

			$level->setTickRate($this->baseTickRate);
		}catch(\Throwable $e){
			$this->logger->error($this->getLanguage()->translateString("pocketmine.level.generateError", [$name, $e->getMessage()]));
			$this->logger->logException($e);
			return false;
		}

		(new LevelInitEvent($level))->call();

		(new LevelLoadEvent($level))->call();

		$this->getLogger()->notice($this->getLanguage()->translateString("pocketmine.level.backgroundGeneration", [$name]));

		$centerX = $level->getSpawnPosition()->getX() >> 4;
		$centerZ = $level->getSpawnPosition()->getZ() >> 4;

		$order = [];

		for($X = -3; $X <= 3; ++$X){
			for($Z = -3; $Z <= 3; ++$Z){
				$distance = $X ** 2 + $Z ** 2;
				$chunkX = $X + $centerX;
				$chunkZ = $Z + $centerZ;
				$index = Level::chunkHash($chunkX, $chunkZ);
				$order[$index] = $distance;
			}
		}

		asort($order);

		foreach($order as $index => $distance){
			Level::getXZ($index, $chunkX, $chunkZ);
			$level->populateChunk($chunkX, $chunkZ, true);
		}

		return true;
	}

	/**
	 * @param string $name
	 *
	 * @return bool
	 */
	public function isLevelGenerated(string $name) : bool{
		if(trim($name) === ""){
			return false;
		}
		$path = $this->getDataPath() . "worlds/" . $name . "/";
		if(!($this->getLevelByName($name) instanceof Level)){

			if(LevelProviderManager::getProvider($path) === null){
				return false;
			}
		}

		return true;
	}

	/**
	 * Searches all levels for the entity with the specified ID.
	 * Useful for tracking entities across multiple worlds without needing strong references.
	 *
	 * @param int        $entityId
	 * @param Level|null $expectedLevel @deprecated Level to look in first for the target
	 *
	 * @return Entity|null
	 */
	public function findEntity(int $entityId, Level $expectedLevel = null){
		foreach($this->levels as $level){
			assert(!$level->isClosed());
			if(($entity = $level->getEntity($entityId)) instanceof Entity){
				return $entity;
			}
		}

		return null;
	}

	/**
	 * @param string $variable
	 * @param mixed  $defaultValue
	 *
	 * @return mixed
	 */
	public function getProperty(string $variable, $defaultValue = null){
		if(!array_key_exists($variable, $this->propertyCache)){
			$v = getopt("", ["$variable::"]);
			if(isset($v[$variable])){
				$this->propertyCache[$variable] = $v[$variable];
			}else{
				$this->propertyCache[$variable] = $this->config->getNested($variable);
			}
		}

		return $this->propertyCache[$variable] ?? $defaultValue;
	}


	/**
	 * @param             $variable
	 * @param null        $defaultValue
	 *
	 * @return bool|mixed|null
	 */
	public function getAdvancedProperty(string $variable, $defaultValue = null){
		$vars = explode(".", $variable);
		$base = array_shift($vars);
		$cfg = $this->advancedConfig;
		if($cfg->exists($base)){
			$base = $cfg->get($base);
		}else{
			return $defaultValue;
		}
		while(count($vars) > 0){
			$baseKey = array_shift($vars);
			if(is_array($base) and isset($base[$baseKey])){
				$base = $base[$baseKey];
			}else{
				return $defaultValue;
			}
		}
		return $base;
	}

	/**
	 * @param string $variable
	 * @param string $defaultValue
	 *
	 * @return string
	 */
	public function getConfigString(string $variable, string $defaultValue = "") : string{
		$v = getopt("", ["$variable::"]);
		if(isset($v[$variable])){
			return (string) $v[$variable];
		}

		return $this->properties->exists($variable) ? (string) $this->properties->get($variable) : $defaultValue;
	}

	/**
	 * @param string $variable
	 * @param string $value
	 */
	public function setConfigString(string $variable, string $value){
		$this->properties->set($variable, $value);
	}

	/**
	 * @param string $variable
	 * @param int    $defaultValue
	 *
	 * @return int
	 */
	public function getConfigInt(string $variable, int $defaultValue = 0) : int{
		$v = getopt("", ["$variable::"]);
		if(isset($v[$variable])){
			return (int) $v[$variable];
		}

		return $this->properties->exists($variable) ? (int) $this->properties->get($variable) : (int) $defaultValue;
	}

	/**
	 * @param string $variable
	 * @param int    $value
	 */
	public function setConfigInt(string $variable, int $value){
		$this->properties->set($variable, (int) $value);
	}

	/**
	 * @param string $variable
	 * @param bool   $defaultValue
	 *
	 * @return bool
	 */
	public function getConfigBoolean(string $variable, bool $defaultValue = false) : bool{
		$v = getopt("", ["$variable::"]);
		if(isset($v[$variable])){
			$value = $v[$variable];
		}else{
			$value = $this->properties->exists($variable) ? $this->properties->get($variable) : $defaultValue;
		}

		if(is_bool($value)){
			return $value;
		}
		switch(strtolower($value)){
			case "on":
			case "true":
			case "1":
			case "yes":
				return true;
		}

		return false;
	}

	/**
	 * @param string $variable
	 * @param bool   $value
	 */
	public function setConfigBool(string $variable, bool $value){
		$this->properties->set($variable, $value == true ? "1" : "0");
	}

	/**
	 * @param string $name
	 *
	 * @return PluginIdentifiableCommand|null
	 */
	public function getPluginCommand(string $name){
		if(($command = $this->commandMap->getCommand($name)) instanceof PluginIdentifiableCommand){
			return $command;
		}else{
			return null;
		}
	}

	/**
	 * @return BanList
	 */
	public function getNameBans(){
		return $this->banByName;
	}

	/**
	 * @return BanList
	 */
	public function getIPBans(){
		return $this->banByIP;
	}

	/**
	 * @param string $name
	 */
	public function addOp(string $name){
		$this->operators->set(strtolower($name), true);

		if(($player = $this->getPlayerExact($name)) !== null){
			$player->recalculatePermissions();
		}
		$this->operators->save(true);
	}

	/**
	 * @param string $name
	 */
	public function removeOp(string $name){
		$this->operators->remove(strtolower($name));

		if(($player = $this->getPlayerExact($name)) !== null){
			$player->recalculatePermissions();
		}
		$this->operators->save();
	}

	/**
	 * @param string $name
	 */
	public function addWhitelist(string $name){
		$this->whitelist->set(strtolower($name), true);
		$this->whitelist->save(true);
	}

	/**
	 * @param string $name
	 */
	public function removeWhitelist(string $name){
		$this->whitelist->remove(strtolower($name));
		$this->whitelist->save();
	}

	/**
	 * @param string $name
	 *
	 * @return bool
	 */
	public function isWhitelisted(string $name) : bool{
		return !$this->hasWhitelist() or $this->operators->exists($name, true) or $this->whitelist->exists($name, true);
	}

	/**
	 * @param string $name
	 *
	 * @return bool
	 */
	public function isOp(string $name) : bool{
		return $this->operators->exists($name, true);
	}

	/**
	 * @return Config
	 */
	public function getWhitelisted(){
		return $this->whitelist;
	}

	/**
	 * @return Config
	 */
	public function getOps(){
		return $this->operators;
	}

	public function reloadWhitelist(){
		$this->whitelist->reload();
	}

	/**
	 * @return string[]
	 */
	public function getCommandAliases() : array{
		$section = $this->getProperty("aliases");
		$result = [];
		if(is_array($section)){
			foreach($section as $key => $value){
				$commands = [];
				if(is_array($value)){
					$commands = $value;
				}else{
					$commands[] = $value;
				}

				$result[$key] = $commands;
			}
		}

		return $result;
	}

	/**
	 * @return Server
	 */
	public static function getInstance() : Server{
		return self::$instance;
	}

	public static function microSleep(int $microseconds){
		Server::$sleeper->synchronized(function(int $ms){
			Server::$sleeper->wait($ms);
		}, $microseconds);
	}

	/**
	 * @param \ClassLoader    $autoloader
	 * @param \ThreadedLogger $logger
	 * @param string          $filePath
	 * @param string          $dataPath
	 * @param string          $pluginPath
	 */
	public function __construct(\ClassLoader $autoloader, \ThreadedLogger $logger, string $filePath, string $dataPath, string $pluginPath){
		self::$instance = $this;
		self::$sleeper = new \Threaded;
		$this->tickSleeper = new SleeperHandler();
		$this->autoloader = $autoloader;
		$this->logger = $logger;

		try{

			$this->filePath = $filePath;
			if(!file_exists($dataPath . "worlds/")){
				mkdir($dataPath . "worlds/", 0777);
			}

			if(!file_exists($dataPath . "players/")){
				mkdir($dataPath . "players/", 0777);
			}

			if(!file_exists($pluginPath)){
				mkdir($pluginPath, 0777);
			}

			$this->dataPath = realpath($dataPath) . DIRECTORY_SEPARATOR;
			$this->pluginPath = realpath($pluginPath) . DIRECTORY_SEPARATOR;

			$consoleNotifier = new SleeperNotifier();
			$this->console = new CommandReader($consoleNotifier);
			$this->tickSleeper->addNotifier($consoleNotifier, function() : void{
				$this->checkConsole();
			});

			$version = new VersionString($this->getPocketMineVersion());

			$this->logger->info("Loading pocketmine.yml...");
			if(!file_exists($this->dataPath . "pocketmine.yml")){
				$content = file_get_contents($this->filePath . "src/pocketmine/resources/pocketmine.yml");
				if($version->isDev()){
					$content = str_replace("preferred-channel: stable", "preferred-channel: beta", $content);
				}
				@file_put_contents($this->dataPath . "pocketmine.yml", $content);
			}
			$this->config = new Config($this->dataPath . "pocketmine.yml", Config::YAML, []);

			$this->logger->info("Loading aquamine.yml...");
			if(!file_exists($this->dataPath . "aquamine.yml")){
				$content = file_get_contents($this->filePath . "src/pocketmine/resources/aquamine.yml");
				@file_put_contents($this->dataPath . "aquamine.yml", $content);
			}
			$this->advancedConfig = new Config($this->dataPath . "aquamine.yml", Config::YAML, []);
			
			$this->logger->setLogToFile(!$this->getAdvancedProperty("server.disable-logging", false));

			$this->logger->info("Loading server properties...");
			$this->properties = new Config($this->dataPath . "server.properties", Config::PROPERTIES, [
				"motd" => "Minecraft: PE Server",
				"server-port" => 19132,
				"white-list" => false,
				"max-players" => 20,
				"allow-flight" => false,
				"spawn-animals" => true,
				"spawn-mobs" => true,
				"gamemode" => 0,
				"force-gamemode" => false,
				"hardcore" => false,
				"pvp" => true,
				"difficulty" => 1,
				"generator-settings" => "",
				"level-name" => "world",
				"level-seed" => "",
				"level-type" => "DEFAULT",
				"enable-query" => true,
				"enable-rcon" => false,
				"rcon.password" => substr(base64_encode(random_bytes(20)), 3, 10),
				"auto-save" => true,
				"online-mode" => false,
				"view-distance" => 8
			]);
 			
 			if($this->getAdvancedProperty("port-range.enabled", false)){
 				$start = (int) $this->getAdvancedProperty("port-range.start");
 				$end = (int) $this->getAdvancedProperty("port-range.end");

 				$port = $start;
 				while(!Utils::isPortAvailable($port) and $port <= $end){
 					++$port;
 				}

				if($port > $end){
					$this->logger->warning("There were no free ports in the range! Using the default port.");
					$this->port = $this->getConfigInt("server-port", 19132);
				}else{
					$this->port = $port;
				}
				
 			}else{
 				$this->port = $this->getConfigInt("server-port", 19132);
 			}

			$this->forceLanguage = $this->getProperty("settings.force-language", false);
			$this->baseLang = new BaseLang($this->getProperty("settings.language", BaseLang::FALLBACK_LANGUAGE));
			$this->logger->info($this->getLanguage()->translateString("language.selected", [$this->getLanguage()->getName(), $this->getLanguage()->getLang()]));

			$this->memoryManager = new MemoryManager($this);

			$this->logger->info($this->getLanguage()->translateString("pocketmine.server.start", [TextFormat::AQUA . $this->getVersion() . TextFormat::RESET . " + " . TextFormat::AQUA . $this->getBedrockVersion() . TextFormat::RESET]));

			if(($poolSize = $this->getProperty("settings.async-workers", "auto")) === "auto"){
				$poolSize = ServerScheduler::$WORKERS;
				$processors = Utils::getCoreCount() - 2;

				if($processors > 0){
					$poolSize = max(1, $processors);
				}
			}

			ServerScheduler::$WORKERS = $poolSize;

			if($this->getProperty("network.batch-threshold", 256) >= 0){
				McpeNetworkCompression::$THRESHOLD = (int) $this->getProperty("network.batch-threshold", 256);
			}else{
				McpeNetworkCompression::$THRESHOLD = -1;
			}
			McpeNetworkCompression::$LEVEL = $this->getProperty("network.compression-level", 7);
			$this->networkCompressionAsync = $this->getProperty("network.async-compression", true);

			BedrockNetworkCompression::$THRESHOLD = McpeNetworkCompression::$THRESHOLD;
			BedrockNetworkCompression::$LEVEL = McpeNetworkCompression::$LEVEL;

			$this->autoTickRate = (bool) $this->getProperty("level-settings.auto-tick-rate", true);
			$this->autoTickRateLimit = (int) $this->getProperty("level-settings.auto-tick-rate-limit", 20);
			$this->alwaysTickPlayers = (int) $this->getProperty("level-settings.always-tick-players", false);
			$this->baseTickRate = (int) $this->getProperty("level-settings.base-tick-rate", 1);

			$this->doTitleTick = (bool) $this->getProperty("console.title-tick", true);

			$this->scheduler = new ServerScheduler();

			if($this->getConfigBoolean("enable-rcon", false) === true){
				try{
					$this->rcon = new RCON(
						$this,
						$this->getConfigString("rcon.password", ""),
						$this->getConfigInt("rcon.port", $this->getPort()),
						($ip = $this->getIp()) != "" ? $ip : "0.0.0.0",
						$this->getConfigInt("rcon.max-clients", 50)
					);
				}catch(\Throwable $e){
					$this->getLogger()->critical("RCON can't be started: " . $e->getMessage());
				}
			}

			$this->entityMetadata = new EntityMetadataStore();
			$this->playerMetadata = new PlayerMetadataStore();
			$this->levelMetadata = new LevelMetadataStore();

			$this->operators = new Config($this->dataPath . "ops.txt", Config::ENUM);
			$this->whitelist = new Config($this->dataPath . "white-list.txt", Config::ENUM);
			if(file_exists($this->dataPath . "banned.txt") and !file_exists($this->dataPath . "banned-players.txt")){
				@rename($this->dataPath . "banned.txt", $this->dataPath . "banned-players.txt");
			}
			@touch($this->dataPath . "banned-players.txt");
			$this->banByName = new BanList($this->dataPath . "banned-players.txt");
			$this->banByName->load();
			@touch($this->dataPath . "banned-ips.txt");
			$this->banByIP = new BanList($this->dataPath . "banned-ips.txt");
			$this->banByIP->load();

			$this->maxPlayers = $this->getConfigInt("max-players", 20);
			$this->setAutoSave($this->getConfigBoolean("auto-save", true));

			if($this->getConfigBoolean("hardcore", false) === true and $this->getDifficulty() < 3){
				$this->setConfigInt("difficulty", 3);
			}

			define('pocketmine\DEBUG', (int) $this->getProperty("debug.level", 1));

			if(((int) ini_get('zend.assertions')) > 0 and ((bool) $this->getProperty("debug.assertions.warn-if-enabled", true)) !== false){
				$this->logger->warning("Debugging assertions are enabled, this may impact on performance. To disable them, set `zend.assertions = -1` in php.ini.");
			}

			ini_set('assert.exception', '1');

			if($this->logger instanceof MainLogger){
				$this->logger->setLogDebug(\pocketmine\DEBUG > 1);
			}

			if(\pocketmine\DEBUG >= 0){
				@cli_set_process_title($this->getName() . " " . $this->getPocketMineVersion() . " " . $this->getPort());
			}

			if(!extension_loaded("pocketmine_chunkutils")){
				$this->logger->warning("pocketmine_chunkutils extension is not loaded, this will have a major impact on performance of Anvil worlds");
			}
			if(!extension_loaded("chunkutils2")){
				$this->logger->warning("chunkutils2 extension is not loaded, this will have a major impact on performance of Minecraft: Bedrock chunks");
			}

			$this->logger->info($this->getLanguage()->translateString("pocketmine.server.networkStart", [$this->getIp() === "" ? "*" : $this->getIp(), $this->getPort()]));
			define("BOOTUP_RANDOM", random_bytes(16));
			$this->serverID = Utils::getMachineUniqueId($this->getIp() . $this->getPort());

			$this->getLogger()->debug("Server unique id: " . $this->getServerUniqueId());
			$this->getLogger()->debug("Machine unique id: " . Utils::getMachineUniqueId());

			$this->network = new Network($this);

			$this->logger->info($this->getLanguage()->translateString("pocketmine.server.info", [
				$this->getName(),
				($version->isDev() ? TextFormat::YELLOW : "") . $version->get(true) . TextFormat::WHITE,
				$this->getCodename(),
				$this->getApiVersion()
			]));

			Timings::init();
			EncryptionContext::$ENABLED = (bool) $this->getProperty("network.enable-encryption", true);

			$this->consoleSender = new ConsoleCommandSender();
			$this->commandMap = new SimpleCommandMap($this);

			Entity::init();
			Tile::init();
			InventoryType::init();
			Block::init();
			Enchantment::init();
			Item::init();
			Biome::init();
			Effect::init();
			Attribute::init();
			$this->craftingManager = new CraftingManager();

			$this->logger->info("Loading PW10 resource packs...");
			$this->pw10ResourcePackManager = new ResourcePackManager($this, $this->getDataPath() . "pw10_packs" . DIRECTORY_SEPARATOR);
			$this->logger->debug("Successfully loaded " . count($this->pw10ResourcePackManager->getResourceStack()) . " resource packs");

			$this->logger->info("Loading bedrock resource packs...");
			$this->bedrockResourcePackManager = new ResourcePackManager($this, $this->getDataPath() . "bedrock_packs" . DIRECTORY_SEPARATOR);
			$this->logger->debug("Successfully loaded " . count($this->bedrockResourcePackManager->getResourceStack()) . " resource packs");

			$this->pluginManager = new PluginManager($this, $this->commandMap);
			$this->pluginManager->subscribeToPermission(Server::BROADCAST_CHANNEL_ADMINISTRATIVE, $this->consoleSender);
			$this->pluginManager->setUseTimings($this->getProperty("settings.enable-profiling", false));
			$this->profilingTickRate = (float) $this->getProperty("settings.profile-report-trigger", 20);
			$this->allowInventoryCheats = $this->getAdvancedProperty("inventory.allow-cheats", false);
			$this->pluginManager->registerInterface(PharPluginLoader::class);
			$this->pluginManager->registerInterface(ScriptPluginLoader::class);

			register_shutdown_function([$this, "crashDump"]);

			$this->queryRegenerateTask = new QueryRegenerateEvent($this, 5);
			$this->network->registerInterface(new RakLibInterface($this));
			
			include($filePath . "src/pocketmine/API.php");
			\API::init();

			$this->pluginManager->loadPlugins($this->pluginPath);

			$this->enablePlugins(PluginLoadOrder::PRESTARTUP);
			$this->enablePlugins(PluginLoadOrder::STARTUP);

			LevelProviderManager::addProvider(Anvil::class);
			LevelProviderManager::addProvider(McRegion::class);
			LevelProviderManager::addProvider(PMAnvil::class);
			if(extension_loaded("leveldb")){
				$this->logger->debug($this->getLanguage()->translateString("pocketmine.debug.enable"));
				LevelProviderManager::addProvider(LevelDB::class);
			}


			Generator::addGenerator(Flat::class, "flat");
			Generator::addGenerator(VoidGenerator::class, "void");
			Generator::addGenerator(Normal::class, "normal");
			Generator::addGenerator(Normal::class, "default");
			Generator::addGenerator(Nether::class, "hell");
			Generator::addGenerator(Nether::class, "nether");

			foreach((array) $this->getProperty("worlds", []) as $name => $worldSetting){
				if($this->loadLevel($name) === false){
					$seed = $this->getProperty("worlds.$name.seed", time());
					$options = explode(":", $this->getProperty("worlds.$name.generator", Generator::getGenerator("default")));
					$generator = Generator::getGenerator(array_shift($options));
					if(count($options) > 0){
						$options = [
							"preset" => implode(":", $options)
						];
					}else{
						$options = [];
					}

					$this->generateLevel($name, $seed, $generator, $options);
				}
			}

			if($this->getDefaultLevel() === null){
				$default = $this->getConfigString("level-name", "world");
				if(trim($default) == ""){
					$this->getLogger()->warning("level-name cannot be null, using default");
					$default = "world";
					$this->setConfigString("level-name", "world");
				}
				if($this->loadLevel($default) === false){
					$seed = getopt("", ["level-seed::"])["level-seed"] ?? $this->properties->get("level-seed", time());
					if(!is_numeric($seed) or bccomp($seed, "9223372036854775807") > 0){
						$seed = Utils::javaStringHash($seed);
					}elseif(PHP_INT_SIZE === 8){
						$seed = (int) $seed;
					}
					$this->generateLevel($default, $seed === 0 ? time() : $seed);
				}

				$this->setDefaultLevel($this->getLevelByName($default));
			}


			if($this->getAdvancedProperty("server.readonly-properties", false)){
				$this->properties->save(true);
			}

			if(!($this->getDefaultLevel() instanceof Level)){
				$this->getLogger()->emergency($this->getLanguage()->translateString("pocketmine.level.defaultError"));
				$this->forceShutdown();

				return;
			}

			if($this->getProperty("ticks-per.autosave", 6000) > 0){
				$this->autoSaveTicks = (int) $this->getProperty("ticks-per.autosave", 6000);
			}

			$this->enablePlugins(PluginLoadOrder::POSTWORLD);

			$this->start();
		}catch(\Throwable $e){
			$this->exceptionHandler($e);
		}
	}

	/**
	 * @param TextContainer|string $message
	 * @param Player[]             $recipients
	 *
	 * @return int
	 */
	public function broadcastMessage($message, array $recipients = null) : int{
		if(!is_array($recipients)){
			return $this->broadcast($message, self::BROADCAST_CHANNEL_USERS);
		}

		/** @var Player[] $recipients */
		foreach($recipients as $recipient){
			$recipient->sendMessage($message);
		}

		return count($recipients);
	}

	/**
	 * @param string   $tip
	 * @param Player[] $recipients
	 *
	 * @return int
	 */
	public function broadcastTip(string $tip, array $recipients = null) : int{
		if(!is_array($recipients)){
			/** @var Player[] $recipients */
			$recipients = [];
			foreach($this->pluginManager->getPermissionSubscriptions(self::BROADCAST_CHANNEL_USERS) as $permissible){
				if($permissible instanceof Player and $permissible->hasPermission(self::BROADCAST_CHANNEL_USERS)){
					$recipients[spl_object_id($permissible)] = $permissible; // do not send messages directly, or some might be repeated
				}
			}
		}

		/** @var Player[] $recipients */
		foreach($recipients as $recipient){
			$recipient->sendTip($tip);
		}

		return count($recipients);
	}

	/**
	 * @param string   $popup
	 * @param Player[] $recipients
	 *
	 * @return int
	 */
	public function broadcastPopup(string $popup, array $recipients = null) : int{
		if(!is_array($recipients)){
			/** @var Player[] $recipients */
			$recipients = [];

			foreach($this->pluginManager->getPermissionSubscriptions(self::BROADCAST_CHANNEL_USERS) as $permissible){
				if($permissible instanceof Player and $permissible->hasPermission(self::BROADCAST_CHANNEL_USERS)){
					$recipients[spl_object_id($permissible)] = $permissible; // do not send messages directly, or some might be repeated
				}
			}
		}

		/** @var Player[] $recipients */
		foreach($recipients as $recipient){
			$recipient->sendPopup($popup);
		}

		return count($recipients);
	}

	/**
	 * @param string $title
	 * @param string $subtitle
	 * @param int    $fadeIn Duration in ticks for fade-in. If -1 is given, client-sided defaults will be used.
	 * @param int    $stay Duration in ticks to stay on screen for
	 * @param int    $fadeOut Duration in ticks for fade-out.
	 * @param Player[]|null $recipients
	 *
	 * @return int
	 */
	public function broadcastTitle(string $title, string $subtitle = "", int $fadeIn = -1, int $stay = -1, int $fadeOut = -1, array $recipients = null) : int{
		if(!is_array($recipients)){
			/** @var Player[] $recipients */
			$recipients = [];

			foreach($this->pluginManager->getPermissionSubscriptions(self::BROADCAST_CHANNEL_USERS) as $permissible){
				if($permissible instanceof Player and $permissible->hasPermission(self::BROADCAST_CHANNEL_USERS)){
					$recipients[spl_object_id($permissible)] = $permissible; // do not send messages directly, or some might be repeated
				}
			}
		}

		/** @var Player[] $recipients */
		foreach($recipients as $recipient){
			$recipient->addTitle($title, $subtitle, $fadeIn, $stay, $fadeOut);
		}

		return count($recipients);
	}

	/**
	 * @param TextContainer|string $message
	 * @param string               $permissions
	 *
	 * @return int
	 */
	public function broadcast($message, string $permissions) : int{
		/** @var CommandSender[] $recipients */
		$recipients = [];
		foreach(explode(";", $permissions) as $permission){
			foreach($this->pluginManager->getPermissionSubscriptions($permission) as $permissible){
				if($permissible instanceof CommandSender and $permissible->hasPermission($permission)){
					$recipients[spl_object_id($permissible)] = $permissible; // do not send messages directly, or some might be repeated
				}
			}
		}

		foreach($recipients as $recipient){
			$recipient->sendMessage($message);
		}

		return count($recipients);
	}

	/**
	 * Broadcasts a Minecraft packet to a list of players
	 *
	 * @param Player[]   $players
	 * @param DataPacket $packet
	 */
	public function broadcastPacket(array $players, DataPacket $packet){
		$packet->encode();
		$this->batchPackets($players, [$packet], false);
	}

	/**
	 * Broadcasts a list of packets in a batch to a list of players
	 *
	 * @param Player[]     $players
	 * @param DataPacket[] $packets
	 * @param bool         $forceSync
	 * @param bool         $immediate
	 */
	public function batchPackets(array $players, array $packets, bool $forceSync = false, bool $immediate = false){
		Timings::$playerNetworkTimer->startTiming();

		$bedrockTargets = [];
		$targets = [];
		foreach($players as $player){
			if($player->isConnected()){
				if($player instanceof BedrockPlayer){
					$bedrockTargets[$player->getProtocolVersion()][] = $this->identifiers[spl_object_id($player)];
				}else{
					$targets[] = $this->identifiers[spl_object_id($player)];
				}
			}
		}

		if(count($targets) > 0){
			$batch = new BatchPacket();

			foreach($packets as $packet){
				$batch->addPacket($packet);
			}

			if(McpeNetworkCompression::$THRESHOLD >= 0 and strlen($batch->payload) >= McpeNetworkCompression::$THRESHOLD){
				$batch->setCompressionLevel(McpeNetworkCompression::$LEVEL);
			}else{
				$batch->setCompressionLevel(0); //Do not compress packets under the threshold
				$forceSync = true;
			}

			if(!$forceSync and !$immediate and $this->networkCompressionAsync){
				$promise = new CompressBatchPromise();

				$task = new McpeCompressBatchedTask($batch->payload, $batch->getCompressionLevel(), $promise);
				$this->getScheduler()->scheduleAsyncTask($task);

				$promise->onResolve(function(CompressBatchPromise $promise) use ($immediate, $targets) : void{
					$this->broadcastPacketsCallback($promise->getResult(), $targets, $immediate);
				});
			}else{
				$this->broadcastPacketsCallback(McpeNetworkCompression::compress($batch->payload, $batch->getCompressionLevel()), $targets, $immediate);
			}
		}
		foreach($bedrockTargets as $protocolVersion => $targets){
			$batch = new BatchPacket();

			$empty = true;
			foreach($packets as $packet){
				if(!$packet instanceof BedrockPacket){
					$packet = PacketTranslator::translate($packet);
				}

				if($packet !== null){
					$adapter = ProtocolAdapterFactory::get($protocolVersion);
					if($adapter !== null){
						$packet = $adapter->processServerToClient($packet);
					}
				}
				if($packet !== null){
					$empty = false;
					$batch->addPacket($packet);
				}
			}

			if(!$empty){
				if(BedrockNetworkCompression::$THRESHOLD >= 0 and strlen($batch->payload) >= BedrockNetworkCompression::$THRESHOLD){
					$batch->setCompressionLevel(BedrockNetworkCompression::$LEVEL);
				}else{
					$batch->setCompressionLevel(0); //Do not compress packets under the threshold
					$forceSync = true;
				}

				if(!$forceSync and !$immediate and $this->networkCompressionAsync){
					$promise = new CompressBatchPromise();

					$task = new BedrockCompressBatchedTask($batch->payload, $batch->getCompressionLevel(), $promise);
					$this->getScheduler()->scheduleAsyncTask($task);

					$promise->onResolve(function(CompressBatchPromise $promise) use ($immediate, $targets) : void{
						$this->broadcastPacketsCallback($promise->getResult(), $targets, $immediate);
					});
				}else{
					$this->broadcastPacketsCallback(BedrockNetworkCompression::compress($batch->payload, $batch->getCompressionLevel()), $targets, $immediate);
				}
			}
		}

		Timings::$playerNetworkTimer->stopTiming();
	}

	public function broadcastPacketsCallback(string $encoded, array $identifiers, bool $immediate = false){
		foreach($identifiers as $i){
			if(isset($this->players[$i])){
				$this->players[$i]->sendEncoded(ProtocolInfo::MCPE_RAKNET_PACKET_ID . $encoded, false, $immediate);
			}
		}
	}


	/**
	 * @param int $type
	 */
	public function enablePlugins(int $type){
		foreach($this->pluginManager->getPlugins() as $plugin){
			if(!$plugin->isEnabled() and $plugin->getDescription()->getOrder() === $type){
				$this->enablePlugin($plugin);
			}
		}

		if($type === PluginLoadOrder::POSTWORLD){
			$this->commandMap->registerServerAliases();
			DefaultPermissions::registerCorePermissions();
		}
	}

	/**
	 * @param Plugin $plugin
	 */
	public function enablePlugin(Plugin $plugin){
		$this->pluginManager->enablePlugin($plugin);
	}

	public function disablePlugins(){
		$this->pluginManager->disablePlugins();
	}

	public function checkConsole(){
		Timings::$serverCommandTimer->startTiming();
		while(($line = $this->console->getLine()) !== null){
			$ev = new ServerCommandEvent($this->consoleSender, $line);
			$ev->call();
			if(!$ev->isCancelled()){
				$this->dispatchCommand($ev->getSender(), $ev->getCommand());
			}
		}
		Timings::$serverCommandTimer->stopTiming();
	}

	/**
	 * Executes a command from a CommandSender
	 *
	 * @param CommandSender $sender
	 * @param string        $commandLine
	 *
	 * @return bool
	 */
	public function dispatchCommand(CommandSender $sender, string $commandLine) : bool{
		if($this->commandMap->dispatch($sender, $commandLine)){
			return true;
		}


		$sender->sendCommandMessage(new TranslationContainer(TextFormat::RED . "%commands.generic.notFound"));

		return false;
	}

	public function reload(){
		$this->logger->info("Saving levels...");

		foreach($this->levels as $level){
			$level->save();
		}

		$this->pluginManager->disablePlugins();
		$this->pluginManager->clearPlugins();
		$this->commandMap->clearCommands();

		$this->logger->info("Reloading properties...");
		$this->properties->reload();
		$this->maxPlayers = $this->getConfigInt("max-players", 20);

		if($this->getConfigBoolean("hardcore", false) === true and $this->getDifficulty() < 3){
			$this->setConfigInt("difficulty", 3);
		}

		$this->banByIP->load();
		$this->banByName->load();
		$this->reloadWhitelist();
		$this->operators->reload();

		foreach($this->getIPBans()->getEntries() as $entry){
			$this->getNetwork()->blockAddress($entry->getName(), -1);
		}

		$this->pluginManager->registerInterface(PharPluginLoader::class);
		$this->pluginManager->registerInterface(ScriptPluginLoader::class);
		$this->pluginManager->loadPlugins($this->pluginPath);
		$this->enablePlugins(PluginLoadOrder::PRESTARTUP);
		$this->enablePlugins(PluginLoadOrder::STARTUP);
		$this->enablePlugins(PluginLoadOrder::POSTWORLD);
		TimingsHandler::reload();
	}

	/**
	 * Shutdowns the server correctly
	 */
	public function shutdown(){
		$this->isRunning = false;
	}

	public function forceShutdown(){
		if($this->hasStopped){
			return;
		}

		try{
			$this->hasStopped = true;

			$this->shutdown();
			if($this->rcon instanceof RCON){
				$this->rcon->stop();
			}

			if($this->getProperty("network.upnp-forwarding", false) === true){
				$this->logger->info("[UPnP] Removing port forward...");
				UPnP::RemovePortForward($this->getPort());
			}

			if($this->pluginManager instanceof PluginManager){
				$this->getLogger()->debug("Disabling all plugins");
				$this->pluginManager->disablePlugins();
			}

			foreach($this->players as $player){
				$player->close($player->getLeaveMessage(), $this->getProperty("settings.shutdown-message", "Server closed"));
			}

			$this->getLogger()->debug("Unloading all levels");
			foreach($this->getLevels() as $level){
				$this->unloadLevel($level, true);
			}

			$this->getLogger()->debug("Removing event handlers");
			HandlerList::unregisterAll();

			if($this->scheduler instanceof ServerScheduler){
				$this->getLogger()->debug("Shutting down task scheduler");
				$this->scheduler->shutdown();
			}

			if($this->getAdvancedProperty("server.readonly-properties", false)){
				$this->getLogger()->debug("Saving properties");
				$this->properties->save();
			}

			$this->getLogger()->debug("Closing console");
			$this->console->shutdown();
			$this->console->notify();

			if($this->network instanceof Network){
				$this->getLogger()->debug("Stopping network interfaces");
				foreach($this->network->getInterfaces() as $interface){
					$interface->shutdown();
					$this->network->unregisterInterface($interface);
				}
			}

			$killer = new ServerKiller(8);
			$killer->start(PTHREADS_INHERIT_NONE);

			usleep(10000); //Fixes ServerKiller not being able to start on single-core machines
		}catch(\Throwable $e){
			$this->logger->logException($e);
			$this->logger->emergency("Crashed while crashing, killing process");
			@kill(getmypid());
		}
	}

	/**
	 * @return QueryRegenerateEvent
	 */
	public function getQueryInformation(){
		return $this->queryRegenerateTask;
	}

	/**
	 * Starts the PocketMine-MP server and starts processing ticks and packets
	 */
	public function start(){
		if($this->getConfigBoolean("enable-query", true) === true){
			$this->queryHandler = new QueryHandler();
		}

		foreach($this->getIPBans()->getEntries() as $entry){
			$this->network->blockAddress($entry->getName(), -1);
		}

		if($this->getProperty("network.upnp-forwarding", false)){
			$this->logger->info("[UPnP] Trying to port forward...");
			UPnP::PortForward($this->getPort());
		}

		$this->tickCounter = 0;

		if(function_exists("pcntl_signal")){
			pcntl_signal(SIGTERM, [$this, "handleSignal"]);
			pcntl_signal(SIGINT, [$this, "handleSignal"]);
			pcntl_signal(SIGHUP, [$this, "handleSignal"]);
			$this->dispatchSignals = true;
		}

		$this->logger->info($this->getLanguage()->translateString("pocketmine.server.defaultGameMode", [self::getGamemodeString($this->getGamemode())]));

		$this->logger->info($this->getLanguage()->translateString("pocketmine.server.startFinished", [round(microtime(true) - \pocketmine\START_TIME, 3)]));

		$this->tickProcessor();
		$this->forceShutdown();
	}

	public function handleSignal($signo){
		if($signo === SIGTERM or $signo === SIGINT or $signo === SIGHUP){
			$this->shutdown();
		}
	}

	public function exceptionHandler(\Throwable $e, $trace = null){
		if($e === null){
			return;
		}

		global $lastError;

		if($trace === null){
			$trace = $e->getTrace();
		}

		$errstr = $e->getMessage();
		$errfile = $e->getFile();
		$errno = $e->getCode();
		$errline = $e->getLine();

		$type = ($errno === E_ERROR or $errno === E_USER_ERROR) ? \LogLevel::ERROR : (($errno === E_USER_WARNING or $errno === E_WARNING) ? \LogLevel::WARNING : \LogLevel::NOTICE);

		$errstr = preg_replace('/\s+/', ' ', trim($errstr));

		$errfile = cleanPath($errfile);

		$this->logger->logException($e, $trace);

		$lastError = [
			"type" => $type,
			"message" => $errstr,
			"fullFile" => $e->getFile(),
			"file" => $errfile,
			"line" => $errline,
			"trace" => getTrace(0, $trace)
		];

		global $lastExceptionError, $lastError;
		$lastExceptionError = $lastError;
		$this->crashDump();
	}

	public function crashDump(){
		if($this->isRunning === false){
			return;
		}
		$this->hasStopped = false;

		ini_set("error_reporting", '0');
		ini_set("memory_limit", '-1'); //Fix error dump not dumped on memory problems
		$this->logger->emergency($this->getLanguage()->translateString("pocketmine.crash.create"));
		try{
			$dump = new CrashDump($this);

			$this->logger->emergency($this->getLanguage()->translateString("pocketmine.crash.submit", [$dump->getPath()]));

			if($this->getProperty("auto-report.enabled", true) !== false){
				$report = true;
				$plugin = $dump->getData()["plugin"];
				if(is_string($plugin)){
					$p = $this->pluginManager->getPlugin($plugin);
					if($p instanceof Plugin and !($p->getPluginLoader() instanceof PharPluginLoader)){
						$report = false;
					}
				}

				if($dump->getData()["error"]["type"] === "E_PARSE" or $dump->getData()["error"]["type"] === "E_COMPILE_ERROR"){
					$report = false;
				}

				if(strrpos(\pocketmine\GIT_COMMIT, "-dirty") !== false or \pocketmine\GIT_COMMIT === str_repeat("00", 20)){
					$this->logger->debug("Not sending crashdump due to locally modified");
					$report = false; //Don't send crashdumps for locally modified builds
				}

				if($report){
					$url = ($this->getProperty("auto-report.use-https", true) ? "https" : "http") . "://" . $this->getProperty("auto-report.host", "crash.pmmp.io") . "/submit/api";
					$reply = Internet::postURL($url, [
						"report" => "yes",
						"name" => $this->getName() . " " . $this->getPocketMineVersion(),
						"email" => "crash@pocketmine.net",
						"reportPaste" => base64_encode($dump->getEncodedData())
					]);

					if($reply !== false and ($data = json_decode($reply)) !== null and isset($data->crashId) and isset($data->crashUrl)){
						$reportId = $data->crashId;
						$reportUrl = $data->crashUrl;
						$this->logger->emergency($this->getLanguage()->translateString("pocketmine.crash.archive", [$reportUrl, $reportId]));
					}
				}
			}
		}catch(\Throwable $e){
			$this->logger->logException($e);
			$this->logger->critical($this->getLanguage()->translateString("pocketmine.crash.error", [$e->getMessage()]));
		}

		//$this->checkMemory();
		//$dump .= "Memory Usage Tracking: \r\n" . chunk_split(base64_encode(gzdeflate(implode(";", $this->memoryStats), 9))) . "\r\n";

		$this->forceShutdown();
		$this->isRunning = false;
		@kill(getmypid());
		exit(1);
	}

	public function __debugInfo(){
		return [];
	}

	public function getTickSleeper() : SleeperHandler{
		return $this->tickSleeper;
	}

	private function tickProcessor(){
		$this->nextTick = microtime(true);
		while($this->isRunning){
			$this->tick();

			//sleeps are self-correcting - if we undersleep 1ms on this tick, we'll sleep an extra ms on the next tick
			$this->tickSleeper->sleepUntil($this->nextTick);
		}
	}

	public function addPlayer($identifier, Player $player){
		$this->players[$identifier] = $player;
		$this->identifiers[spl_object_id($player)] = $identifier;
	}

	public function addOnlinePlayer(Player $player){
		$this->updatePlayerList($player);
		$this->playerList[$player->getRawUniqueId()] = $player;
	}

	public function removeOnlinePlayer(Player $player){
		if(isset($this->playerList[$player->getRawUniqueId()])){
			unset($this->playerList[$player->getRawUniqueId()]);
			$this->removePlayerList($player->getUniqueId());
		}
	}

	public function updatePlayerList(Player $player) : void{
		foreach($this->playerList as $onlinePlayer){
			$onlinePlayer->updatePlayerList($player);
		}
	}

	public function removePlayerList(UUID $uuid) : void{
		foreach($this->playerList as $onlinePlayer){
			$onlinePlayer->removePlayerList($uuid);
		}
	}

	private function checkTickUpdates($currentTick, $tickTime){
		foreach($this->players as $p){
			if(!$p->loggedIn and ($tickTime - $p->creationTime) >= 10){
				$p->close("", "Login timeout");
			}elseif($this->alwaysTickPlayers and $p->joined){
				$p->onUpdate($currentTick);
			}
		}

		//Do level ticks
		foreach($this->getLevels() as $level){
			if($level->getTickRate() > $this->baseTickRate and --$level->tickRateCounter > 0){
				continue;
			}
			try{
				$levelTime = microtime(true);
				$level->doTick($currentTick);
				$tickMs = (microtime(true) - $levelTime) * 1000;
				$level->tickRateTime = $tickMs;

				if($this->autoTickRate){
					if($tickMs < 50 and $level->getTickRate() > $this->baseTickRate){
						$level->setTickRate($r = $level->getTickRate() - 1);
						if($r > $this->baseTickRate){
							$level->tickRateCounter = $level->getTickRate();
						}
						$this->getLogger()->debug("Raising level \"{$level->getName()}\" tick rate to {$level->getTickRate()} ticks");
					}elseif($tickMs >= 50){
						if($level->getTickRate() === $this->baseTickRate){
							$level->setTickRate(max($this->baseTickRate + 1, min($this->autoTickRateLimit, (int) floor($tickMs / 50))));
							$this->getLogger()->debug(sprintf("Level \"%s\" took %gms, setting tick rate to %d ticks", $level->getName(), (int) round($tickMs, 2), $level->getTickRate()));
						}elseif(($tickMs / $level->getTickRate()) >= 50 and $level->getTickRate() < $this->autoTickRateLimit){
							$level->setTickRate($level->getTickRate() + 1);
							$this->getLogger()->debug(sprintf("Level \"%s\" took %gms, setting tick rate to %d ticks", $level->getName(), (int) round($tickMs, 2), $level->getTickRate()));
						}
						$level->tickRateCounter = $level->getTickRate();
					}
				}
			}catch(\Throwable $e){
				$this->logger->critical($this->getLanguage()->translateString("pocketmine.level.tickError", [$level->getName(), $e->getMessage()]));
				$this->logger->logException($e);
			}
		}
	}

	public function doAutoSave(){
		if($this->getAutoSave()){
			Timings::$worldSaveTimer->startTiming();
			foreach($this->players as $index => $player){
				if($player->joined){
					$player->save(true);
				}elseif(!$player->isConnected()){
					$this->removePlayer($player);
				}
			}

			foreach($this->getLevels() as $level){
				$level->save(false);
			}
			Timings::$worldSaveTimer->stopTiming();
		}
	}


	/**
	 * @return BaseLang
	 */
	public function getLanguage(){
		return $this->baseLang;
	}

	/**
	 * @return bool
	 */
	public function isLanguageForced() : bool{
		return $this->forceLanguage;
	}

	/**
	 * @return Network
	 */
	public function getNetwork(){
		return $this->network;
	}

	/**
	 * @return MemoryManager
	 */
	public function getMemoryManager(){
		return $this->memoryManager;
	}

	/**
	 * @return bool|null
	 */
	public function isNetworkCompressionAsync(){
		return $this->networkCompressionAsync;
	}

	private function titleTick(){
		Timings::$titleTickTimer->startTiming();
		$d = Utils::getRealMemoryUsage();

		$u = Utils::getMemoryUsage(true);
		$usage = sprintf("%g/%g/%g/%g MB @ %d threads", round(($u[0] / 1024) / 1024, 2), round(($d[0] / 1024) / 1024, 2), round(($u[1] / 1024) / 1024, 2), round(($u[2] / 1024) / 1024, 2), Utils::getThreadCount());

		echo "\x1b]0;" . $this->getName() . " " .
			$this->getPocketMineVersion() .
			" | Online " . count($this->players) . "/" . $this->getMaxPlayers() .
			" | Memory " . $usage .
			" | U " . round($this->network->getUpload() / 1024, 2) .
			" D " . round($this->network->getDownload() / 1024, 2) .
			" kB/s | TPS " . $this->getTicksPerSecondAverage() .
			" | Load " . $this->getTickUsageAverage() . "%\x07";

		$this->network->resetStatistics();

		Timings::$titleTickTimer->stopTiming();
	}

	/**
	 * @param string $address
	 * @param int    $port
	 * @param string $payload
	 *
	 * TODO: move this to Network
	 */
	public function handlePacket(string $address, int $port, string $payload){
		try{
			if(strlen($payload) > 2 and substr($payload, 0, 2) === "\xfe\xfd" and $this->queryHandler instanceof QueryHandler){
				$this->queryHandler->handle($address, $port, $payload);
			}
		}catch(\Throwable $e){
			if(\pocketmine\DEBUG > 1){
				$this->logger->logException($e);
			}

			$this->getNetwork()->blockAddress($address, 600);
		}
		//TODO: add raw packet events
	}


	/**
	 * Tries to execute a server tick
	 */
	private function tick() : bool{
		$tickTime = microtime(true);
		if(($tickTime - $this->nextTick) < -0.025){ //Allow half a tick of diff
			return false;
		}

		Timings::$serverTickTimer->startTiming();

		++$this->tickCounter;

		Timings::$connectionTimer->startTiming();
		$this->network->processInterfaces();

		Timings::$connectionTimer->stopTiming();

		Timings::$schedulerTimer->startTiming();
		$this->scheduler->mainThreadHeartbeat($this->tickCounter);
		Timings::$schedulerTimer->stopTiming();

		$this->checkTickUpdates($this->tickCounter, $tickTime);

		foreach($this->players as $player){
			$player->checkNetwork();
		}

		if($this->tickCounter % 20 === 0){
			if($this->doTitleTick and Terminal::hasFormattingCodes()){
				$this->titleTick();
			}
			$this->currentTPS = 20;
			$this->currentUse = 0;

			try{
				$this->queryRegenerateTask = new QueryRegenerateEvent($this, 5);
				$this->queryRegenerateTask->call();
				if($this->queryHandler !== null){
					$this->queryHandler->regenerateInfo();
				}
			}catch(\Throwable $e){
				$this->logger->logException($e);
			}
		}

		if($this->autoSave and ++$this->autoSaveTicker >= $this->autoSaveTicks){
			$this->autoSaveTicker = 0;
			$this->doAutoSave();
		}

		if(($this->tickCounter % 100) === 0){
			foreach($this->levels as $level){
				$level->clearCache();
			}

			if($this->getTicksPerSecondAverage() < 12){
				$this->logger->warning($this->getLanguage()->translateString("pocketmine.server.tickOverload"));
			}
		}

		if($this->dispatchSignals and $this->tickCounter % 5 === 0){
			pcntl_signal_dispatch();
		}

		$this->getMemoryManager()->check();

		Timings::$serverTickTimer->stopTiming();

		$now = microtime(true);
		$this->currentTPS = min(20, 1 / max(0.001, $now - $tickTime));
		$this->currentUse = min(1, ($now - $tickTime) / 0.05);

		TimingsHandler::tick($this->currentTPS <= $this->profilingTickRate);

		$idx = $this->tickCounter % 20;
		$this->tickAverage[$idx] = $this->currentTPS;
		$this->useAverage[$idx] = $this->currentUse;

		if(($this->nextTick - $tickTime) < -1){
			$this->nextTick = $tickTime;
		}else{
			$this->nextTick += 0.05;
		}

		return true;
	}

	/**
	 * Called when something attempts to serialize the server instance.
	 *
	 * @throws \BadMethodCallException because Server instances cannot be serialized
	 */
	public function __sleep(){
		throw new \BadMethodCallException("Cannot serialize Server instance");
	}
}
