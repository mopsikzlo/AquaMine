<?php

declare(strict_types=1);

/**
 * All the Item classes
 */
namespace pocketmine\item;

use InvalidArgumentException;
use InvalidStateException;
use pocketmine\block\Block;
use pocketmine\block\BlockToolType;
use pocketmine\entity\Entity;
use pocketmine\inventory\Fuel;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\nbt\LittleEndianNbtSerializer;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\ShortTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\nbt\TreeRoot;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\Binary;
use pocketmine\utils\Config;
use RuntimeException;
use function bin2hex;
use function constant;
use function defined;
use function explode;
use function get_class;
use function hex2bin;
use function is_numeric;
use function str_replace;
use function strtoupper;
use function trim;

class Item implements ItemIds, \JsonSerializable{

	private static function parseCompoundTag(string $tag) : CompoundTag{
		return (new LittleEndianNbtSerializer())->read($tag)->mustGetCompoundTag();
	}

	private static function writeCompoundTag(CompoundTag $tag) : string{
		return (new LittleEndianNbtSerializer())->write(new TreeRoot($tag));
	}

	/** @var \SplFixedArray */
	public static $list = null;
	/** @var Block|null */
	protected $block;
	/** @var int */
	protected $id;
	/** @var int */
	protected $meta;
	/** @var CompoundTag|null */
	private $nbt;
	/** @var int */
	public $count;
	/** @var string */
	protected $name;

	public static function init(){
		if(self::$list === null){
			self::$list = new \SplFixedArray(65536);

			self::$list[self::IRON_SHOVEL] = IronShovel::class;
			self::$list[self::IRON_PICKAXE] = IronPickaxe::class;
			self::$list[self::IRON_AXE] = IronAxe::class;
			self::$list[self::FLINT_STEEL] = FlintSteel::class;
			self::$list[self::APPLE] = Apple::class;
			self::$list[self::BOW] = Bow::class;
			self::$list[self::ARROW] = Arrow::class;
			self::$list[self::COAL] = Coal::class;
			self::$list[self::DIAMOND] = Diamond::class;
			self::$list[self::IRON_INGOT] = IronIngot::class;
			self::$list[self::GOLD_INGOT] = GoldIngot::class;
			self::$list[self::IRON_SWORD] = IronSword::class;
			self::$list[self::WOODEN_SWORD] = WoodenSword::class;
			self::$list[self::WOODEN_SHOVEL] = WoodenShovel::class;
			self::$list[self::WOODEN_PICKAXE] = WoodenPickaxe::class;
			self::$list[self::WOODEN_AXE] = WoodenAxe::class;
			self::$list[self::STONE_SWORD] = StoneSword::class;
			self::$list[self::STONE_SHOVEL] = StoneShovel::class;
			self::$list[self::STONE_PICKAXE] = StonePickaxe::class;
			self::$list[self::STONE_AXE] = StoneAxe::class;
			self::$list[self::DIAMOND_SWORD] = DiamondSword::class;
			self::$list[self::DIAMOND_SHOVEL] = DiamondShovel::class;
			self::$list[self::DIAMOND_PICKAXE] = DiamondPickaxe::class;
			self::$list[self::DIAMOND_AXE] = DiamondAxe::class;
			self::$list[self::STICK] = Stick::class;
			self::$list[self::BOWL] = Bowl::class;
			self::$list[self::MUSHROOM_STEW] = MushroomStew::class;
			self::$list[self::GOLDEN_SWORD] = GoldSword::class;
			self::$list[self::GOLDEN_SHOVEL] = GoldShovel::class;
			self::$list[self::GOLDEN_PICKAXE] = GoldPickaxe::class;
			self::$list[self::GOLDEN_AXE] = GoldAxe::class;
			self::$list[self::STRING] = StringItem::class;
			self::$list[self::FEATHER] = Feather::class;
			self::$list[self::GUNPOWDER] = Gunpowder::class;
			self::$list[self::WOODEN_HOE] = WoodenHoe::class;
			self::$list[self::STONE_HOE] = StoneHoe::class;
			self::$list[self::IRON_HOE] = IronHoe::class;
			self::$list[self::DIAMOND_HOE] = DiamondHoe::class;
			self::$list[self::GOLDEN_HOE] = GoldHoe::class;
			self::$list[self::WHEAT_SEEDS] = WheatSeeds::class;
			self::$list[self::WHEAT] = Wheat::class;
			self::$list[self::BREAD] = Bread::class;
			self::$list[self::LEATHER_CAP] = LeatherCap::class;
			self::$list[self::LEATHER_TUNIC] = LeatherTunic::class;
			self::$list[self::LEATHER_PANTS] = LeatherPants::class;
			self::$list[self::LEATHER_BOOTS] = LeatherBoots::class;
			self::$list[self::CHAINMAIL_HELMET] = ChainHelmet::class;
			self::$list[self::CHAINMAIL_CHESTPLATE] = ChainChestplate::class;
			self::$list[self::CHAINMAIL_LEGGINGS] = ChainLeggings::class;
			self::$list[self::CHAINMAIL_BOOTS] = ChainBoots::class;
			self::$list[self::IRON_HELMET] = IronHelmet::class;
			self::$list[self::IRON_CHESTPLATE] = IronChestplate::class;
			self::$list[self::IRON_LEGGINGS] = IronLeggings::class;
			self::$list[self::IRON_BOOTS] = IronBoots::class;
			self::$list[self::DIAMOND_HELMET] = DiamondHelmet::class;
			self::$list[self::DIAMOND_CHESTPLATE] = DiamondChestplate::class;
			self::$list[self::DIAMOND_LEGGINGS] = DiamondLeggings::class;
			self::$list[self::DIAMOND_BOOTS] = DiamondBoots::class;
			self::$list[self::GOLDEN_HELMET] = GoldHelmet::class;
			self::$list[self::GOLDEN_CHESTPLATE] = GoldChestplate::class;
			self::$list[self::GOLDEN_LEGGINGS] = GoldLeggings::class;
			self::$list[self::GOLDEN_BOOTS] = GoldBoots::class;
			self::$list[self::FLINT] = Flint::class;
			self::$list[self::RAW_PORKCHOP] = RawPorkchop::class;
			self::$list[self::COOKED_PORKCHOP] = CookedPorkchop::class;
			self::$list[self::PAINTING] = Painting::class;
			self::$list[self::GOLDEN_APPLE] = GoldenApple::class;
			self::$list[self::SIGN] = Sign::class;
			self::$list[self::WOODEN_DOOR] = WoodenDoor::class;
			self::$list[self::BUCKET] = Bucket::class;

			self::$list[self::MINECART] = Minecart::class;

			self::$list[self::IRON_DOOR] = IronDoor::class;
			self::$list[self::REDSTONE] = Redstone::class;
			self::$list[self::SNOWBALL] = Snowball::class;
			self::$list[self::BOAT] = Boat::class;
			self::$list[self::LEATHER] = Leather::class;

			self::$list[self::BRICK] = Brick::class;
			self::$list[self::CLAY] = Clay::class;
			self::$list[self::SUGARCANE] = Sugarcane::class;
			self::$list[self::PAPER] = Paper::class;
			self::$list[self::BOOK] = Book::class;
			self::$list[self::SLIMEBALL] = Slimeball::class;

			self::$list[self::EGG] = Egg::class;
			self::$list[self::COMPASS] = Compass::class;
			self::$list[self::FISHING_ROD] = FishingRod::class;
			self::$list[self::CLOCK] = Clock::class;
			self::$list[self::GLOWSTONE_DUST] = GlowstoneDust::class;
			self::$list[self::RAW_FISH] = Fish::class;
			self::$list[self::COOKED_FISH] = CookedFish::class;
			self::$list[self::DYE] = Dye::class;
			self::$list[self::BONE] = Bone::class;
			self::$list[self::SUGAR] = Sugar::class;
			self::$list[self::CAKE] = Cake::class;
			self::$list[self::BED] = Bed::class;

			self::$list[self::COOKIE] = Cookie::class;

			self::$list[self::SHEARS] = Shears::class;
			self::$list[self::MELON] = Melon::class;
			self::$list[self::PUMPKIN_SEEDS] = PumpkinSeeds::class;
			self::$list[self::MELON_SEEDS] = MelonSeeds::class;
			self::$list[self::RAW_BEEF] = RawBeef::class;
			self::$list[self::STEAK] = Steak::class;
			self::$list[self::RAW_CHICKEN] = RawChicken::class;
			self::$list[self::COOKED_CHICKEN] = CookedChicken::class;

			self::$list[self::ENDER_PEARL] = EnderPearl::class;

			self::$list[self::GOLD_NUGGET] = GoldNugget::class;
			self::$list[self::NETHER_WART] = NetherWart::class;
			self::$list[self::POTION] = Potion::class;
			self::$list[self::GLASS_BOTTLE] = GlassBottle::class;
			self::$list[self::SPIDER_EYE] = SpiderEye::class;
			self::$list[self::FERMENTED_SPIDER_EYE] = FermentedSpiderEye::class;
			self::$list[self::BLAZE_POWDER] = BlazePowder::class;
			self::$list[self::MAGMA_CREAM] = MagmaCream::class;
			self::$list[self::BREWING_STAND] = BrewingStand::class;

			self::$list[self::GLISTERING_MELON] = GlisteringMelon::class;
			self::$list[self::SPAWN_EGG] = SpawnEgg::class;

			self::$list[self::EMERALD] = Emerald::class;
			self::$list[self::ITEM_FRAME] = ItemFrame::class;
			self::$list[self::FLOWER_POT] = FlowerPot::class;
			self::$list[self::CARROT] = Carrot::class;
			self::$list[self::POTATO] = Potato::class;
			self::$list[self::BAKED_POTATO] = BakedPotato::class;

			self::$list[self::GOLDEN_CARROT] = GoldenCarrot::class;
			self::$list[self::SKULL] = Skull::class;

			self::$list[self::NETHER_STAR] = NetherStar::class;
			self::$list[self::PUMPKIN_PIE] = PumpkinPie::class;

			self::$list[self::NETHER_BRICK] = NetherBrick::class;
			self::$list[self::NETHER_QUARTZ] = NetherQuartz::class;

			self::$list[self::PRISMARINE_SHARD] = PrismarineShard::class;

			self::$list[self::COOKED_RABBIT] = CookedRabbit::class;

			self::$list[self::PRISMARINE_CRYSTALS] = PrismarineCrystals::class;

			self::$list[self::BEETROOT] = Beetroot::class;
			self::$list[self::BEETROOT_SEEDS] = BeetrootSeeds::class;
			self::$list[self::BEETROOT_SOUP] = BeetrootSoup::class;

			self::$list[self::SPLASH_POTION] = SplashPotion::class;

			self::$list[self::ELYTRA] = Elytra::class;

			self::$list[self::TOTEM] = Totem::class;

			self::$list[self::ENCHANTED_GOLDEN_APPLE] = GoldenAppleEnchanted::class;
		}

		self::initCreativeItems();
	}

	private static $creative = [];

	private static function initCreativeItems(){
		self::clearCreativeItems();

		$creativeItems = new Config(Server::getInstance()->getFilePath() . "src/pocketmine/resources/creativeitems.json", Config::JSON, []);

		foreach($creativeItems->getAll() as $data){
			$item = Item::jsonDeserialize($data);
			if($item->getName() === "Unknown"){
				continue;
			}
			self::addCreativeItem($item);
		}
	}

	public static function clearCreativeItems(){
		Item::$creative = [];
	}

	public static function getCreativeItems() : array{
		return Item::$creative;
	}

	public static function addCreativeItem(Item $item){
		Item::$creative[] = clone $item;
	}

	public static function removeCreativeItem(Item $item){
		$index = self::getCreativeItemIndex($item);
		if($index !== -1){
			unset(Item::$creative[$index]);
		}
	}

	public static function isCreativeItem(Item $item) : bool{
		foreach(Item::$creative as $i => $d){
			if($item->equals($d, !$item->isTool())){
				return true;
			}
		}

		return false;
	}

	/**
	 * @param $index
	 *
	 * @return Item|null
	 */
	public static function getCreativeItem(int $index){
		return Item::$creative[$index] ?? null;
	}

	public static function getCreativeItemIndex(Item $item) : int{
		foreach(Item::$creative as $i => $d){
			if($item->equals($d, !($item instanceof Durable))){
				return $i;
			}
		}

		return -1;
	}

	/**
	 * Returns an instance of the Item with the specified id, meta, count and NBT.
	 *
	 * @param int                $id
	 * @param int                $meta
	 * @param int                $count
	 * @param CompoundTag|string $tags
	 *
	 * @return Item
	 */
	public static function get(int $id, int $meta = 0, int $count = 1, $tags = "") : Item{
		try{
			if($id < 256){
				return (new ItemBlock(Block::get($id, $meta), $meta, $count))->setCompoundTag($tags);
			}else{
				if(isset(self::$list[$id])) {
					$class = self::$list[$id];
				} else {
					$class = null;
				}
				if($class === null){
					return (new Item($id, $meta, $count))->setCompoundTag($tags);
				}else{
					return (new $class($meta, $count))->setCompoundTag($tags);
				}
			}
		}catch(RuntimeException $e){
			return (new Item($id, $meta, $count))->setCompoundTag($tags);
		}
	}

	/**
	 * @return Item
	 */
	public static function air() : Item{
		return Item::get(Item::AIR, 0, 0);
	}

	/**
	 * @param string $str
	 * @param bool   $multiple
	 *
	 * @return Item[]|Item
	 */
	public static function fromString(string $str, bool $multiple = false){
		if($multiple === true){
			$blocks = [];
			foreach(explode(",", $str) as $b){
				$blocks[] = self::fromString($b, false);
			}

			return $blocks;
		}else{
			$b = explode(":", str_replace([" ", "minecraft:"], ["_", ""], trim($str)));
			if(!isset($b[1])){
				$meta = 0;
			}else{
				$meta = $b[1] & 0xFFFF;
			}

			if(defined(Item::class . "::" . strtoupper($b[0]))){
				$item = self::get(constant(Item::class . "::" . strtoupper($b[0])), $meta);
				if($item->getId() === self::AIR and strtoupper($b[0]) !== "AIR"){
					$item = self::get($b[0] & 0xFFFF, $meta);
				}
			}elseif(is_numeric($b[0])){
				$item = self::get($b[0] & 0xFFFF, $meta);
			}else{
				return self::get(self::AIR, 0, 0);
			}

			return $item;
		}
	}

	/**
	 * @param int $id
	 * @param int $meta
	 * @param int $count
	 * @param string $name
	 */
	public function __construct(int $id, int $meta = 0, int $count = 1, string $name = "Unknown"){
		$this->id = $id & 0xffff;
		$this->meta = $meta !== -1 ? $meta & 0xffff : -1;
		$this->count = $count;
		$this->name = $name;
		if(!isset($this->block) and $this->id <= 0xff and isset(Block::$list[$this->id])){
			$this->block = Block::get($this->id, $this->meta);
			$this->name = $this->block->getName();
		}
	}

	/**
	 * Sets the Item's NBT
	 * @deprecated This method accepts NBT serialized in a network-dependent format.
	 * @see Item::setNamedTag()
	 *
	 * @param CompoundTag|string $tags
	 *
	 * @return $this
	 */
	public function setCompoundTag($tags){
		if($tags instanceof CompoundTag){
			$this->setNamedTag($tags);
		}elseif(is_string($tags) and strlen($tags) > 0){
			$this->setNamedTag(self::parseCompoundTag($tags));
		}else{
			$this->clearNamedTag();
		}

		return $this;
	}

	/**
	 * Returns the serialized NBT of the Item
	 * @return string
	 */
	public function getCompoundTag() : string{
		return $this->nbt !== null ? self::writeCompoundTag($this->nbt) : "";
	}

	/**
	 * Returns whether this Item has a non-empty NBT.
	 * @return bool
	 */
	public function hasCompoundTag() : bool{
		return $this->nbt !== null and $this->nbt->getCount() > 0;
	}

	/**
	 * @return bool
	 */
	public function hasCustomBlockData() : bool{
		return $this->getNamedTag()->hasTag("BlockEntityTag", CompoundTag::class);
	}

	public function clearCustomBlockData(){
		if($this->getNamedTag()->hasTag("BlockEntityTag", CompoundTag::class)){
			$this->getNamedTag()->removeTag("BlockEntityTag");
		}

		return $this;
	}

	/**
	 * @param CompoundTag $compound
	 *
	 * @return $this
	 */
	public function setCustomBlockData(CompoundTag $compound){
		$this->getNamedTag()->setTag("BlockEntityTag", clone $compound);
		return $this;
	}

	/**
	 * @return CompoundTag|null
	 */
	public function getCustomBlockData(){
		$tag = $this->getNamedTag();
		if($tag->hasTag("BlockEntityTag", CompoundTag::class)){
			return $tag->getCompoundTag("BlockEntityTag");
		}

		return null;
	}

	/**
	 * @return bool
	 */
	public function hasEnchantments() : bool{
		return $this->getNamedTag()->hasTag("ench", ListTag::class);
	}

	/**
	 * @param int $id
	 * @param int $level
	 *
	 * @return bool
	 */
	public function hasEnchantment(int $id, int $level = -1) : bool{
		if(!$this->hasEnchantments()){
			return false;
		}

		foreach($this->getNamedTag()->getListTag("ench") as $tag){
			/** @var CompoundTag $tag */
			if($tag->getShort("id") === $id){
				if($level === -1 or $tag->getShort("lvl") === $level){
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * @param int $id
	 *
	 * @return Enchantment|null
	 */
	public function getEnchantment(int $id){
		if(!$this->hasEnchantments()){
			return null;
		}

		foreach($this->getNamedTag()->getListTag("ench") as $tag){
			/** @var CompoundTag $tag */
			if($tag->getShort("id") === $id){
				$e = Enchantment::getEnchantment($id);
				if($e !== null){
					$e->setLevel($tag->getShort("lvl"));
					return $e;
				}
			}
		}

		return null;
	}

	/**
	 * @param int $id
	 * @param int $level
	 */
	public function removeEnchantment(int $id, int $level = -1){
		if(!$this->hasEnchantments()){
			return;
		}

		$tag = $this->getNamedTag();
		$enchTag = $tag->getListTag("ench");
		foreach($enchTag as $k => $tag){
			/** @var CompoundTag $tag */
			if($tag->getShort("id") === $id){
				if($level === -1 or $tag->getShort("lvl") === $level){
					$enchTag->remove($k);
					break;
				}
			}
		}
	}

	public function removeEnchantments(){
		$this->getNamedTag()->removeTag("ench");
	}

	/**
	 * @param Enchantment $ench
	 *
	 * @return $this
	 */
	public function addEnchantment(Enchantment $ench){
		$tag = $this->getNamedTag();

		$found = false;

		$enchList = $tag->getListTag("ench");
		if($enchList === null or $enchList->getTagType() !== NBT::TAG_Compound){
			$tag->setTag("ench", $enchList = new ListTag([], NBT::TAG_Compound));
		}else{
			foreach($enchList as $enchTag){
				/** @var CompoundTag $enchTag */
				if($enchTag->getShort("id") === $ench->getId()){
					$enchTag->setShort("lvl", $ench->getLevel());
					$found = true;
					break;
				}
			}
		}

		if(!$found){
			$enchList->push(CompoundTag::create()
				->setShort("id", $ench->getId())
				->setShort("lvl", $ench->getLevel()));
		}

		return $this;
	}

	/**
	 * @return Enchantment[]
	 */
	public function getEnchantments() : array{
		$enchantments = [];

		if($this->hasEnchantments()){
			foreach($this->getNamedTag()->getListTag("ench") as $entry){
				/** @var CompoundTag $entry */
				$e = Enchantment::getEnchantment($entry->getShort("id"));
				if($e !== null){
					$e->setLevel($entry->getShort("lvl"));
					$enchantments[] = $e;
				}
			}
		}

		return $enchantments;
	}

	public function hasRepairCost() : bool{
		return $this->getNamedTag()->hasTag("RepairCost", IntTag::class);
	}

	public function getRepairCost() : int{
		$tag = $this->getNamedTag();
		return $tag->hasTag("RepairCost", IntTag::class) ? $tag->getInt("RepairCost") : 1;
	}


	public function setRepairCost(int $cost){
		if($cost === 1){
			$this->clearRepairCost();
			return $this;
		}

		$this->getNamedTag()->setInt("RepairCost", $cost);
		return $this;
	}

	public function clearRepairCost(){
		$tag = $this->getNamedTag();
		if($tag->hasTag("RepairCost", IntTag::class)){
			$tag->removeTag("RepairCost");
			$this->setNamedTag($tag);
		}

		return $this;
	}

	/**
	 * @return bool
	 */
	public function hasCustomName() : bool{
		$tag = $this->getNamedTag();
		return $tag->hasTag("display", CompoundTag::class) and $tag->getCompoundTag("display")->hasTag("Name", StringTag::class);
	}

	/**
	 * @return string
	 */
	public function getCustomName() : string{
		$tag = $this->getNamedTag();
		if($tag->hasTag("display", CompoundTag::class)){
			$display = $tag->getCompoundTag("display");
			if($display->hasTag("Name", StringTag::class)){
				return $display->getString("Name");
			}
		}

		return "";
	}

	/**
	 * @param string $name
	 *
	 * @return $this
	 */
	public function setCustomName(string $name){
		if($name === ""){
			$this->clearCustomName();
			return $this;
		}

		$tag = $this->getNamedTag();
		if(!$tag->hasTag("display", CompoundTag::class)){
			$tag->setTag("display", new CompoundTag());
		}
		$tag->getCompoundTag("display")->setString("Name", $name);

		return $this;
	}

	/**
	 * @return $this
	 */
	public function clearCustomName(){
		$tag = $this->getNamedTag();
		if($tag->hasTag("display", CompoundTag::class)){
			$display = $tag->getCompoundTag("display");
			if($display->hasTag("Name", StringTag::class)){
				$display->removeTag("Name");
			}
		}

		return $this;
	}

	public function getLore() : array{
		/** @var CompoundTag $tag */
		$tag = $this->getNamedTag()->getTag("display", CompoundTag::class);
		if($tag->hasTag("Lore", ListTag::class)){
			$lines = [];
			foreach($tag->getListTag("Lore") as $line){
				$lines[] = $line->getValue();
			}

			return $lines;
		}

		return [];
	}

	/**
	 * @param string[] $lines
	 *
	 * @return $this
	 */
	public function setLore(array $lines){
		$tag = $this->getNamedTag();
		if(!$tag->hasTag("display", CompoundTag::class)){
			$tag->setTag("display", new CompoundTag());
		}

		$displayTag = $tag->getCompoundTag("display");
		$displayTag->setTag("Lore", $loreTag = new ListTag([], NBT::TAG_String));

		foreach($lines as $line){
			$loreTag->push(new StringTag($line));
		}

		return $this;
	}

	/**
	 * Returns a tree of Tag objects representing the Item's NBT
	 * @return null|CompoundTag
	 */
	public function getNamedTag(){
		return $this->nbt ?? ($this->nbt = new CompoundTag());
	}

	/**
	 * Sets the Item's NBT from the supplied CompoundTag object.
	 * @param CompoundTag $tag
	 *
	 * @return $this
	 */
	public function setNamedTag(CompoundTag $tag){
		if($tag->getCount() === 0){
			return $this->clearNamedTag();
		}

		$this->nbt = $tag;
		return $this;
	}

	/**
	 * Removes the Item's NBT.
	 * @return Item
	 */
	public function clearNamedTag(){
		$this->nbt = null;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getCount() : int{
		return $this->count;
	}

	/**
	 * @param int $count
	 */
	public function setCount(int $count){
		$this->count = $count;
	}

	/**
	 * Returns the name of the item, or the custom name if it is set.
	 * @return string
	 */
	final public function getName() : string{
		return $this->hasCustomName() ? $this->getCustomName() : $this->name;
	}

	/**
	 * Returns the vanilla name of the item, disregarding custom names.
	 * @return string
	 */
	public function getVanillaName() : string{
		return $this->name;
	}

	/**
	 * Pops an item from the stack and returns it, decreasing the stack count of this item stack by one.
	 * @return Item
	 *
	 * @throws InvalidStateException if the count is less than or equal to zero, or if the stack is air.
	 */
	public function pop() : Item{
		if($this->isNull()){
			throw new InvalidStateException("Cannot pop an item from a null stack");
		}

		$item = clone $this;
		$item->setCount(1);

		$this->count--;

		return $item;
	}

	/**
	 * @return bool
	 */
	final public function canBePlaced() : bool{
		return $this->block !== null and $this->block->canBePlaced();
	}

	/**
	 * Returns whether an entity can eat or drink this item.
	 * @return bool
	 */
	public function canBeConsumed() : bool{
		return false;
	}

	/**
	 * Returns whether this item can be consumed by the supplied Entity.
	 * @param Entity $entity
	 *
	 * @return bool
	 */
	public function canBeConsumedBy(Entity $entity) : bool{
		return $this->canBeConsumed();
	}

	/**
	 * Called when the item is consumed by an Entity.
	 * @param Entity $entity
	 */
	public function onConsume(Entity $entity){

	}

	/**
	 * Returns the block corresponding to this Item.
	 * @return Block
	 */
	public function getBlock() : Block{
		if($this->block instanceof Block){
			return clone $this->block;
		}else{
			return Block::get(self::AIR);
		}
	}

	/**
	 * @return int
	 */
	final public function getId() : int{
		return $this->id;
	}

	/**
	 * @return int
	 */
	final public function getDamage() : int{
		return $this->meta;
	}

	/**
	 * @param int $meta
	 */
	public function setDamage(int $meta){
		$this->meta = $meta !== -1 ? $meta & 0xFFFF : -1;
	}

	/**
	 * Returns whether this item can match any item with an equivalent ID with any meta value.
	 * Used in crafting recipes which accept multiple variants of the same item, for example crafting tables recipes.
	 *
	 * @return bool
	 */
	public function hasAnyDamageValue() : bool{
		return $this->meta === -1;
	}

	/**
	 * Returns the highest amount of this item which will fit into one inventory slot.
	 * @return int
	 */
	public function getMaxStackSize(){
		return 64;
	}

	final public function getFuelTime(){
		if(!isset(Fuel::$duration[$this->id])){
			return null;
		}
		if($this->id !== self::BUCKET or $this->meta === 10){
			return Fuel::$duration[$this->id];
		}

		return null;
	}

    /**
     * Returns how many points of damage this item will deal to an entity when used as a weapon.
     */
    public function getAttackPoints() : int{
        return 1;
    }

    /**
     * Returns how many armor points can be gained by wearing this item.
     */
    public function getArmorPoints() : int{
        return 0;
    }

    /**
     * Returns what type of block-breaking tool this is. Blocks requiring the same tool type as the item will break
     * faster (except for blocks requiring no tool, which break at the same speed regardless of the tool used)
     */
    public function getBlockToolType() : int{
        return Tool::TYPE_NONE;
    }

    /**
     * Returns the harvesting power that this tool has. This affects what blocks it can mine when the tool type matches
     * the mined block.
     * This should return 1 for non-tiered tools, and the tool tier for tiered tools.
     *
     * @see Block::getToolHarvestLevel()
     */
    public function getBlockToolHarvestLevel() : int{
        return 0;
    }

    public function getMiningEfficiency(Block $block) : float{
        return 1;
    }

	/**
	 * Called when a player is using this item and releases it. Used to handle bow shoot actions.
	 *
	 * @param Player $player
	 */
	public function onReleaseUsing(Player $player) : void{

	}

    /**
     * Called when this item is used to destroy a block. Usually used to update durability.
     */
    public function onDestroyBlock(Block $block) : bool{
        return false;
    }

    /**
     * Called when this item is used to attack an entity. Usually used to update durability.
     */
    public function onAttackEntity(Entity $victim) : bool{
        return false;
    }

	/**
	 * Returns the number of ticks a player must wait before activating this item again.
	 *
	 * @return int
	 */
	public function getCooldownTicks() : int{
		return 0;
	}

	/**
	 * @return bool
	 */
	public function isTool(){
		return false;
	}

	/**
	 * @return int|bool
	 */
	public function getMaxDurability(){
		return false;
	}

	/** @deprecated */
	public function isPickaxe(){
		return false;
	}

	/** @deprecated  */
	public function isAxe(){
		return false;
	}

	/** @deprecated  */
	public function isSword(){
		return false;
	}

	/** @deprecated */
	public function isShovel(){
		return false;
	}

	public function isHoe(){
		return $this instanceof Hoe;
	}

	public function isShears(){
		return false;
	}


	public function getDiamondArmorPoints() : int{
		return 0x300 + ($this->hasEnchantment(Enchantment::PROTECTION) ? $this->getEnchantment(Enchantment::PROTECTION)->getLevel() : 0);
	}

	public function getIronArmorPoints() : int{
		return 0x200 + ($this->hasEnchantment(Enchantment::PROTECTION) ? $this->getEnchantment(Enchantment::PROTECTION)->getLevel() : 0);
	}

	public function getChainArmorPoints() : int{
		return 0x100 + ($this->hasEnchantment(Enchantment::PROTECTION) ? $this->getEnchantment(Enchantment::PROTECTION)->getLevel() : 0);
	}

	public function getLeatherArmorPoints() : int{
		return 0x00 + ($this->hasEnchantment(Enchantment::PROTECTION) ? $this->getEnchantment(Enchantment::PROTECTION)->getLevel() : 0);
	}

	/**
	 * Called when a player uses this item on a block.
	 *
	 * @param Level $level
	 * @param Player $player
	 * @param Block $block
	 * @param Block $target
	 * @param int $face
	 * @param float $fx
	 * @param float $fy
	 * @param float $fz
	 *
	 * @return bool
	 */
	public function onActivate(Level $level, Player $player, Block $block, Block $target, $face, $fx, $fy, $fz){
		return false;
	}

	/**
	 * Called when a player uses the item on air, for example throwing a projectile.
	 * Returns whether the item was changed, for example count decrease or durability change.
	 *
	 * @param Player  $player
	 * @param Vector3 $directionVector
	 *
	 * @return bool
	 */
	public function onClickAir(Player $player, Vector3 $directionVector) : bool{
		return false;
	}

	/**
	 * Returns if an item can be used by clicking air.
	 * 
	 * @return bool
	 */
	public function canBeUsedOnAir() : bool{
		return false;
	}

	/**
	 * @return bool
	 */
	public function isNull() : bool{
		return $this->id === self::AIR or $this->count < 1;
	}

	/**
	 * Compares an Item to this Item and check if they match.
	 *
	 * @param Item $item
	 * @param bool $checkDamage Whether to verify that the damage values match.
	 * @param bool $checkCompound Whether to verify that the items' NBT match.
	 * @param bool $checkCount
	 *
	 * @return bool
	 */
	final public function equals(Item $item, bool $checkDamage = true, bool $checkCompound = true, bool $checkCount = false) : bool{
		if($this->id === $item->getId() and ($checkDamage === false or $this->getDamage() === $item->getDamage()) and ($checkCount === false or $this->getCount() === $item->getCount())){
			if($checkCompound){
				return $this->getNamedTag()->equals($item->getNamedTag());
			}else{
				return true;
			}
		}

		return false;
	}

	/**
	 * @deprecated Use {@link Item#equals} instead, this method will be removed in the future.
	 *
	 * @param Item $item
	 * @param bool $checkDamage
	 * @param bool $checkCompound
	 *
	 * @return bool
	 */
	final public function deepEquals(Item $item, bool $checkDamage = true, bool $checkCompound = true) : bool{
		return $this->equals($item, $checkDamage, $checkCompound);
	}

	/**
	 * @return string
	 */
	final public function __toString() : string{
		return "Item " . $this->name . " (" . $this->id . ":" . ($this->hasAnyDamageValue() ? "?" : $this->meta) . ")x" . $this->count . ($this->hasCompoundTag() ? " tags:0x" . bin2hex($this->getCompoundTag()) : "");
	}

	/**
	 * Returns an array of item stack properties that can be serialized to json.
	 *
	 * @return array
	 */
	final public function jsonSerialize(){
		return [
			"id" => $this->getId(),
			"damage" => $this->getDamage(),
			"count" => $this->getCount(),
			"nbt_hex" => bin2hex($this->getCompoundTag())
		];
	}

	/**
	 * Returns an Item from properties created in an array by {@link Item#jsonSerialize}
	 *
	 * @param array $data
	 * @return Item
	 */
	final public static function jsonDeserialize(array $data) : Item{
		return Item::get(
			(int) $data["id"],
			(int) $data["damage"],
			(int) $data["count"],
			(string) ($data["nbt"] ?? hex2bin($data["nbt_hex"])) //`nbt` key might contain old raw data
		);
	}

	/**
	 * Serializes the item to an NBT CompoundTag
	 *
	 * @param int $slot optional, the inventory slot of the item
	 *
	 * @return CompoundTag
	 */
	public function nbtSerialize(int $slot = -1) : CompoundTag{
		$tag = CompoundTag::create()
			->setShort("id", $this->id)
			->setByte("Count", Binary::signByte($this->count))
			->setShort("Damage", $this->meta);

		if($this->hasCompoundTag()){
			$tag->setTag("tag", clone $this->getNamedTag());
		}

		if($slot !== -1){
			$tag->setByte("Slot", $slot);
		}

		return $tag;
	}

	/**
	 * Deserializes an Item from an NBT CompoundTag
	 *
	 * @param CompoundTag $tag
	 *
	 * @return Item
	 */
	public static function nbtDeserialize(CompoundTag $tag) : Item{
		if(!$tag->hasTag("id") or !$tag->hasTag("Count")){
			return Item::air();
		}

		$count = Binary::unsignByte($tag->getByte("Count"));
		$meta = $tag->getShort("Damage", 0);

		$idTag = $tag->getTag("id");
		if($idTag instanceof ShortTag){
			$item = Item::get($idTag->getValue(), $meta, $count);
		}elseif($idTag instanceof StringTag){ //PC item save format
			$item = Item::fromString($idTag->getValue());
			$item->setDamage($meta);
			$item->setCount($count);
		}else{
			throw new InvalidArgumentException("Item CompoundTag ID must be an instance of StringTag or ShortTag, " . get_class($tag->id) . " given");
		}

		if($tag->hasTag("tag", CompoundTag::class)){
			$item->setNamedTag(clone $tag->getCompoundTag("tag"));
		}

		return $item;
	}

	public function __clone(){
		if($this->block !== null){
			$this->block = clone $this->block;
		}
		if($this->nbt !== null){
			$this->nbt = clone $this->nbt;
		}
	}
}
