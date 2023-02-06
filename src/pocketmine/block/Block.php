<?php

declare(strict_types=1);

/**
 * All Block classes are in here
 */
namespace pocketmine\block;

use pocketmine\entity\Entity;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item;
use pocketmine\item\Tool;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\RayTraceResult;
use pocketmine\math\Vector3;
use pocketmine\metadata\Metadatable;
use pocketmine\metadata\MetadataValue;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use function array_merge;

class Block extends Position implements BlockIds, Metadatable{

	/** @var \SplFixedArray<Block> */
	public static $list = null;
	/** @var \SplFixedArray<Block> */
	public static $fullList = null;

	/** @var \SplFixedArray<int> */
	public static $light = null;
	/** @var \SplFixedArray<int> */
	public static $lightFilter = null;
	/** @var \SplFixedArray<bool> */
	public static $solid = null;
	/** @var \SplFixedArray<float> */
	public static $hardness = null;
	/** @var \SplFixedArray<bool> */
	public static $transparent = null;
	/** @var \SplFixedArray<bool> */
	public static $diffusesSkyLight = null;
	/** @var \SplFixedArray */
	public static $blastResistance = null;

	/**
	 * Initializes the block factory. By default this is called only once on server start, however you may wish to use
	 * this if you need to reset the block factory back to its original defaults for whatever reason.
	 *
	 * @param bool $force
	 */
	public static function init(bool $force = false){
		if(self::$list === null or $force){
			self::$list = new \SplFixedArray(256);
			self::$fullList = new \SplFixedArray(4096);
			self::$light = new \SplFixedArray(256);
			self::$lightFilter = new \SplFixedArray(256);
			self::$solid = new \SplFixedArray(256);
			self::$hardness = new \SplFixedArray(256);
			self::$transparent = new \SplFixedArray(256);
			self::$diffusesSkyLight = new \SplFixedArray(256);
			self::$blastResistance = new \SplFixedArray(256);

			self::registerBlock(new Air());
			self::registerBlock(new Stone());
			self::registerBlock(new Grass());
			self::registerBlock(new Dirt());
			self::registerBlock(new Cobblestone());
			self::registerBlock(new Planks());
			self::registerBlock(new Sapling());
			self::registerBlock(new Bedrock());
			self::registerBlock(new Water());
			self::registerBlock(new StillWater());
			self::registerBlock(new Lava());
			self::registerBlock(new StillLava());
			self::registerBlock(new Sand());
			self::registerBlock(new Gravel());
			self::registerBlock(new GoldOre());
			self::registerBlock(new IronOre());
			self::registerBlock(new CoalOre());
			self::registerBlock(new Wood());
			self::registerBlock(new Leaves());
			self::registerBlock(new Sponge());
			self::registerBlock(new Glass());
			self::registerBlock(new LapisOre());
			self::registerBlock(new Lapis());

			self::registerBlock(new Sandstone());
			self::registerBlock(new NoteBlock());
			self::registerBlock(new Bed());
			self::registerBlock(new PoweredRail());
			self::registerBlock(new DetectorRail());

			self::registerBlock(new Cobweb());
			self::registerBlock(new TallGrass());
			self::registerBlock(new DeadBush());

			self::registerBlock(new Wool());

			self::registerBlock(new Dandelion());
			self::registerBlock(new Flower());
			self::registerBlock(new BrownMushroom());
			self::registerBlock(new RedMushroom());
			self::registerBlock(new Gold());
			self::registerBlock(new Iron());
			self::registerBlock(new DoubleStoneSlab());
			self::registerBlock(new StoneSlab());
			self::registerBlock(new Bricks());
			self::registerBlock(new TNT());
			self::registerBlock(new Bookshelf());
			self::registerBlock(new MossyCobblestone());
			self::registerBlock(new Obsidian());
			self::registerBlock(new Torch());
			self::registerBlock(new Fire());
			self::registerBlock(new MonsterSpawner());
			self::registerBlock(new WoodenStairs(Block::OAK_STAIRS, 0, "Oak Stairs"));
			self::registerBlock(new Chest());

			self::registerBlock(new DiamondOre());
			self::registerBlock(new Diamond());
			self::registerBlock(new CraftingTable());
			self::registerBlock(new Wheat());
			self::registerBlock(new Farmland());
			self::registerBlock(new Furnace());
			self::registerBlock(new BurningFurnace());
			self::registerBlock(new SignPost());
			self::registerBlock(new WoodenDoor(Block::OAK_DOOR_BLOCK, 0, "Oak Door Block", Item::OAK_DOOR));
			self::registerBlock(new Ladder());
			self::registerBlock(new Rail());
			self::registerBlock(new CobblestoneStairs());
			self::registerBlock(new WallSign());
			self::registerBlock(new Lever());
			self::registerBlock(new StonePressurePlate());
			self::registerBlock(new IronDoor());
			self::registerBlock(new WoodenPressurePlate());
			self::registerBlock(new RedstoneOre());
			self::registerBlock(new GlowingRedstoneOre());
			self::registerBlock(new RedstoneTorchUnlit());
			self::registerBlock(new RedstoneTorch());
			self::registerBlock(new StoneButton());
			self::registerBlock(new SnowLayer());
			self::registerBlock(new Ice());
			self::registerBlock(new Snow());
			self::registerBlock(new Cactus());
			self::registerBlock(new Clay());
			self::registerBlock(new Sugarcane());

			self::registerBlock(new Fence());
			self::registerBlock(new Pumpkin());
			self::registerBlock(new Netherrack());
			self::registerBlock(new SoulSand());
			self::registerBlock(new Glowstone());

			self::registerBlock(new LitPumpkin());
			self::registerBlock(new Cake());

			self::registerBlock(new InvisibleBedrock());
			self::registerBlock(new Trapdoor());

			self::registerBlock(new StoneBricks());

			self::registerBlock(new IronBars());
			self::registerBlock(new GlassPane());
			self::registerBlock(new Melon());
			self::registerBlock(new PumpkinStem());
			self::registerBlock(new MelonStem());
			self::registerBlock(new Vine());
			self::registerBlock(new FenceGate(Block::OAK_FENCE_GATE, 0, "Oak Fence Gate"));
			self::registerBlock(new BrickStairs());
			self::registerBlock(new StoneBrickStairs());
			self::registerBlock(new Mycelium());
			self::registerBlock(new WaterLily());
			self::registerBlock(new NetherBrick());
			self::registerBlock(new NetherBrickFence());
			self::registerBlock(new NetherBrickStairs());
			self::registerBlock(new NetherWartPlant());
			self::registerBlock(new EnchantingTable());
			self::registerBlock(new BrewingStand());

			self::registerBlock(new EndPortalFrame());
			self::registerBlock(new EndStone());

			self::registerBlock(new RedstoneLamp());
			self::registerBlock(new LitRedstoneLamp());

			self::registerBlock(new ActivatorRail());
			self::registerBlock(new CocoaBlock());
			self::registerBlock(new SandstoneStairs());
			self::registerBlock(new EmeraldOre());
			self::registerBlock(new EnderChest());
			self::registerBlock(new TripwireHook());
			self::registerBlock(new Tripwire());
			self::registerBlock(new Emerald());
			self::registerBlock(new WoodenStairs(Block::SPRUCE_STAIRS, 0, "Spruce Stairs"));
			self::registerBlock(new WoodenStairs(Block::BIRCH_STAIRS, 0, "Birch Stairs"));
			self::registerBlock(new WoodenStairs(Block::JUNGLE_STAIRS, 0, "Jungle Stairs"));

			self::registerBlock(new CobblestoneWall());
			self::registerBlock(new FlowerPot());
			self::registerBlock(new Carrot());
			self::registerBlock(new Potato());
			self::registerBlock(new WoodenButton());
			self::registerBlock(new Skull());
			self::registerBlock(new Anvil());
			self::registerBlock(new TrappedChest());
			self::registerBlock(new WeightedPressurePlateLight());
			self::registerBlock(new WeightedPressurePlateHeavy());

			self::registerBlock(new DaylightSensor());
			self::registerBlock(new Redstone());

			self::registerBlock(new Quartz());
			self::registerBlock(new QuartzStairs());
			self::registerBlock(new DoubleWoodenSlab());
			self::registerBlock(new WoodenSlab());
			self::registerBlock(new StainedClay());
			self::registerBlock(new StainedGlassPane());

			self::registerBlock(new Leaves2());
			self::registerBlock(new Wood2());
			self::registerBlock(new WoodenStairs(Block::ACACIA_STAIRS, 0, "Acacia Stairs"));
			self::registerBlock(new WoodenStairs(Block::DARK_OAK_STAIRS, 0, "Dark Oak Stairs"));

			self::registerBlock(new IronTrapdoor());
			self::registerBlock(new Prismarine());
			self::registerBlock(new SeaLantern());
			self::registerBlock(new HayBale());
			self::registerBlock(new Carpet());
			self::registerBlock(new HardenedClay());
			self::registerBlock(new Coal());
			self::registerBlock(new PackedIce());
			self::registerBlock(new DoublePlant());

			self::registerBlock(new RedSandstone());
			self::registerBlock(new RedSandstoneStairs());
			self::registerBlock(new DoubleStoneSlab2());
			self::registerBlock(new StoneSlab2());
			self::registerBlock(new FenceGate(Block::SPRUCE_FENCE_GATE, 0, "Spruce Fence Gate"));
			self::registerBlock(new FenceGate(Block::BIRCH_FENCE_GATE, 0, "Birch Fence Gate"));
			self::registerBlock(new FenceGate(Block::JUNGLE_FENCE_GATE, 0, "Jungle Fence Gate"));
			self::registerBlock(new FenceGate(Block::DARK_OAK_FENCE_GATE, 0, "Dark Oak Fence Gate"));
			self::registerBlock(new FenceGate(Block::ACACIA_FENCE_GATE, 0, "Acacia Fence Gate"));

			self::registerBlock(new WoodenDoor(Block::SPRUCE_DOOR_BLOCK, 0, "Spruce Door Block", Item::SPRUCE_DOOR));
			self::registerBlock(new WoodenDoor(Block::BIRCH_DOOR_BLOCK, 0, "Birch Door Block", Item::BIRCH_DOOR));
			self::registerBlock(new WoodenDoor(Block::JUNGLE_DOOR_BLOCK, 0, "Jungle Door Block", Item::JUNGLE_DOOR));
			self::registerBlock(new WoodenDoor(Block::ACACIA_DOOR_BLOCK, 0, "Acacia Door Block", Item::ACACIA_DOOR));
			self::registerBlock(new WoodenDoor(Block::DARK_OAK_DOOR_BLOCK, 0, "Dark Oak Door Block", Item::DARK_OAK_DOOR));
			self::registerBlock(new GrassPath());
			self::registerBlock(new ItemFrame());
			self::registerBlock(new Purpur());
			self::registerBlock(new PurpurStairs());
			self::registerBlock(new EndStoneBricks());
			self::registerBlock(new EndRod());

			self::registerBlock(new GlazedTerracotta(Block::PURPLE_GLAZED_TERRACOTTA, 0, "Purple Glazed Terracotta"));
			self::registerBlock(new GlazedTerracotta(Block::WHITE_GLAZED_TERRACOTTA, 0, "White Glazed Terracotta"));
			self::registerBlock(new GlazedTerracotta(Block::ORANGE_GLAZED_TERRACOTTA, 0, "Orange Glazed Terracotta"));
			self::registerBlock(new GlazedTerracotta(Block::MAGENTA_GLAZED_TERRACOTTA, 0, "Magenta Glazed Terracotta"));
			self::registerBlock(new GlazedTerracotta(Block::LIGHT_BLUE_GLAZED_TERRACOTTA, 0, "Light Blue Glazed Terracotta"));
			self::registerBlock(new GlazedTerracotta(Block::YELLOW_GLAZED_TERRACOTTA, 0, "Yellow Glazed Terracotta"));
			self::registerBlock(new GlazedTerracotta(Block::LIME_GLAZED_TERRACOTTA, 0, "Lime Glazed Terracotta"));
			self::registerBlock(new GlazedTerracotta(Block::PINK_GLAZED_TERRACOTTA, 0, "Pink Glazed Terracotta"));
			self::registerBlock(new GlazedTerracotta(Block::GRAY_GLAZED_TERRACOTTA, 0, "Grey Glazed Terracotta"));
			self::registerBlock(new GlazedTerracotta(Block::SILVER_GLAZED_TERRACOTTA, 0, "Light Grey Glazed Terracotta"));
			self::registerBlock(new GlazedTerracotta(Block::CYAN_GLAZED_TERRACOTTA, 0, "Cyan Glazed Terracotta"));

			self::registerBlock(new GlazedTerracotta(Block::BLUE_GLAZED_TERRACOTTA, 0, "Blue Glazed Terracotta"));
			self::registerBlock(new GlazedTerracotta(Block::BROWN_GLAZED_TERRACOTTA, 0, "Brown Glazed Terracotta"));
			self::registerBlock(new GlazedTerracotta(Block::GREEN_GLAZED_TERRACOTTA, 0, "Green Glazed Terracotta"));
			self::registerBlock(new GlazedTerracotta(Block::RED_GLAZED_TERRACOTTA, 0, "Red Glazed Terracotta"));
			self::registerBlock(new GlazedTerracotta(Block::BLACK_GLAZED_TERRACOTTA, 0, "Black Glazed Terracotta"));

			self::registerBlock(new StainedGlass());

			self::registerBlock(new Podzol());
			self::registerBlock(new Beetroot());
			self::registerBlock(new Stonecutter());
			self::registerBlock(new GlowingObsidian());

			foreach(self::$list as $id => $block){
				if($block === null){
					self::registerBlock(new UnknownBlock($id));
				}
			}
		}
	}

	/**
	 * Registers a block type into the index. Plugins may use this method to register new block types or override
	 * existing ones.
	 *
	 * NOTE: If you are registering a new block type, you will need to add it to the creative inventory yourself - it
	 * will not automatically appear there.
	 *
	 * @param Block $block
	 * @param bool  $override Whether to override existing registrations
	 *
	 * @throws \RuntimeException if something attempted to override an already-registered block without specifying the
	 * $override parameter.
	 */
	public static function registerBlock(Block $block, bool $override = false){
		$id = $block->getId();

		if(self::$list[$id] !== null and !(self::$list[$id] instanceof UnknownBlock) and !$override){
			throw new \RuntimeException("Trying to overwrite an already registered block");
		}

		self::$list[$id] = clone $block;

		for($meta = 0; $meta < 16; ++$meta){
			$variant = clone $block;
			$variant->setDamage($meta);
			self::$fullList[($id << 4) | $meta] = $variant;
		}

		self::$solid[$id] = $block->isSolid();
		self::$transparent[$id] = $block->isTransparent();
		self::$hardness[$id] = $block->getHardness();
		self::$light[$id] = $block->getLightLevel();
		self::$lightFilter[$id] = $block->getLightFilter() + 1; //opacity plus 1 standard light filter
		self::$diffusesSkyLight[$id] = $block->diffusesSkyLight();
		self::$blastResistance[$id] = $block->getBlastResistance();
	}

	/**
	 * @param int      $id
	 * @param int      $meta
	 * @param Position $pos
	 *
	 * @return Block
	 */
	public static function get($id, $meta = 0, Position $pos = null){
		try{
			if(!isset(self::$fullList[($id << 4) | $meta])) {
				$block = null;
			} else {
				$block = self::$fullList[($id << 4) | $meta];
			}
			if($block !== null){
				$block = clone $block;
			}else{
				$block = new UnknownBlock($id, $meta);
			}
		}catch(\RuntimeException $e){
			//TODO: this probably should return null (out of bounds IDs may cause unexpected behaviour)
			$block = new UnknownBlock($id, $meta);
		}

		if($pos !== null){
			$block->x = $pos->x;
			$block->y = $pos->y;
			$block->z = $pos->z;
			$block->level = $pos->level;
		}

		return $block;
	}


	protected $id;
	protected $meta = 0;
	/** @var string */
	protected $fallbackName;
	/** @var int|null */
	protected $itemId;

	/** @var AxisAlignedBB */
	public $boundingBox = null;

	/** @var AxisAlignedBB[]|null */
	protected $collisionBoxes = null;

	/**
	 * @param int    $id     The block type's ID, 0-255
	 * @param int    $meta   Meta value of the block type
	 * @param string $name   English name of the block type (TODO: implement translations)
	 * @param int    $itemId The item ID of the block type, used for block picking and dropping items.
	 */
	public function __construct(int $id, int $meta = 0, string $name = "Unknown", int $itemId = null){
		$this->id = $id;
		$this->meta = $meta;
		$this->fallbackName = $name;
		$this->itemId = $itemId;
	}

	/**
	 * @return string
	 */
	public function getName(){
		return $this->fallbackName;
	}

	/**
	 * @return int
	 */
	final public function getId(){
		return $this->id;
	}

	/**
	 * Returns the ID of the item form of the block.
	 * Used for drops for blocks (some blocks such as doors have a different item ID).
	 *
	 * @return int
	 */
	public function getItemId() : int{
		return $this->itemId ?? $this->getId();
	}

	/**
	 * @return int
	 */
	final public function getDamage(){
		return $this->meta;
	}

	/**
	 * @param int $meta
	 */
	final public function setDamage($meta){
		$this->meta = $meta & 0x0f;
	}

	/**
	 * Places the Block, using block space and block target, and side. Returns if the block has been placed.
	 *
	 * @param Item   $item
	 * @param Block  $block
	 * @param Block  $target
	 * @param int    $face
	 * @param float  $fx
	 * @param float  $fy
	 * @param float  $fz
	 * @param Player $player = null
	 *
	 * @return bool
	 */
	public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null){
		return $this->getLevel()->setBlock($this, $this, true, true);
	}

	/**
	 * Returns if the block can be broken with an specific Item
	 *
	 * @param Item $item
	 *
	 * @return bool
	 */
	public function isBreakable(Item $item){
		return true;
	}

    public function getToolType(){
        return Tool::TYPE_NONE;
    }

    /**
     * Returns the level of tool required to harvest this block (for normal blocks). When the tool type matches the
     * block's required tool type, the tool must have a harvest level greater than or equal to this value to be able to
     * successfully harvest the block.
     *
     * If the block requires a specific minimum tier of tiered tool, the minimum tier required should be returned.
     * Otherwise, 1 should be returned if a tool is required, 0 if not.
     *
     * @see Item::getBlockToolHarvestLevel()
     */
    public function getToolHarvestLevel() : int{
        return 0;
    }

    /**
     * Returns whether the specified item is the proper tool to use for breaking this block. This checks tool type and
     * harvest level requirement.
     *
     * In most cases this is also used to determine whether block drops should be created or not, except in some
     * special cases such as vines.
     */
    public function isCompatibleWithTool(Item $tool) : bool{
        if($this->getHardness() < 0){
            return false;
        }

        $toolType = $this->getToolType();
        $harvestLevel = $this->getToolHarvestLevel();
        return $toolType === Tool::TYPE_NONE or $harvestLevel === 0 or (
                ($toolType & $tool->getBlockToolType()) !== 0 and $tool->getBlockToolHarvestLevel() >= $harvestLevel);
    }

	/**
	 * Do the actions needed so the block is broken with the Item
	 *
	 * @param Item $item
	 *
	 * @return mixed
	 */
	public function onBreak(Item $item){
		return $this->getLevel()->setBlock($this, new Air(), true, true);
	}

	/**
	 * Fires a block update on the Block
	 *
	 * @param int $type
	 *
	 * @return int|bool
	 */
	public function onUpdate($type){
		return false;
	}

	/**
	 * Do actions when activated by Item. Returns if it has done anything
	 *
	 * @param Item   $item
	 * @param Player $player
	 *
	 * @return bool
	 */
	public function onActivate(Item $item, Player $player = null){
		return false;
	}

	/**
	 * @return bool
	 */
	public function canBeActivated() : bool{
		return false;
	}

	/**
	 * @return float
	 */
	public function getHardness(){
		return 10;
	}

	/**
	 * @deprecated
	 * @return float
	 */
	public function getResistance() : float{
		return $this->getBlastResistance();
	}

	/**
	 * Returns the block's resistance to explosions. Usually 5x hardness.
	 * @return float
	 */
	public function getBlastResistance() : float{
		return $this->getHardness() * 5;
	}

	/**
	 * @return float
	 */
	public function getFrictionFactor(){
		return 0.6;
	}

	/**
	 * @return int 0-15
	 */
	public function getLightLevel(){
		return 0;
	}

	/**
	 * Returns the amount of light this block will filter out when light passes through this block.
	 * This value is used in light spread calculation.
	 *
	 * @return int 0-15
	 */
	public function getLightFilter() : int{
		return 15;
	}

	/**
	 * Returns whether this block will diffuse sky light passing through it vertically.
	 * Diffusion means that full-strength sky light passing through this block will not be reduced, but will start being filtered below the block.
	 * Examples of this behaviour include leaves and cobwebs.
	 *
	 * Light-diffusing blocks are included by the heightmap.
	 *
	 * @return bool
	 */
	public function diffusesSkyLight() : bool{
		return false;
	}

	/**
	 * AKA: Block->isPlaceable
	 *
	 * @return bool
	 */
	public function canBePlaced(){
		return true;
	}

	/**
	 * @return bool
	 */
	public function canBeReplaced(){
		return false;
	}

	/**
	 * @return bool
	 */
	public function isTransparent(){
		return false;
	}

	public function isSolid(){
		return true;
	}

	/**
	 * AKA: Block->isFlowable
	 *
	 * @return bool
	 */
	public function canBeFlowedInto(){
		return false;
	}

	public function hasEntityCollision(){
		return false;
	}

	public function canPassThrough(){
		return false;
	}

	/**
	 * Returns whether entities can climb up this block.
	 * @return bool
	 */
	public function canClimb() : bool{
		return false;
	}


	public function addVelocityToEntity(Entity $entity, Vector3 $vector){

	}

	/**
	 * Sets the block position to a new Position object
	 *
	 * @param Position $v
	 */
	final public function position(Position $v){
		$this->x = (int) $v->x;
		$this->y = (int) $v->y;
		$this->z = (int) $v->z;
		$this->level = $v->level;
		$this->boundingBox = null;
	}

	/**
	 * Returns an array of Item objects to be dropped
	 *
	 * @param Item $item
	 *
	 * @return array
	 */
	public function getDrops(Item $item){
		if(!isset(self::$list[$this->getId()])){ //Unknown blocks
			return [];
		}else{
			return [
				[$this->getItemId(), $this->getDamage(), 1],
			];
		}
	}

	/**
	 * Returns the item that players will equip when middle-clicking on this block.
	 *
	 * @return Item
	 */
	public function getPickedItem() : Item{
		return Item::get($this->getItemId(), $this->getDamage());
	}

    /**
     * Returns the seconds that this block takes to be broken using an specific Item
     *
     * @throws \InvalidArgumentException if the item efficiency is not a positive number
     */
    public function getBreakTime(Item $item) : float{
        $base = $this->getHardness();
        if($this->isCompatibleWithTool($item)){
            $base *= 1.5;
        }else{
            $base *= 5;
        }

        $efficiency = $item->getMiningEfficiency($this);
        if($efficiency <= 0){
            throw new \InvalidArgumentException(get_class($item) . " has invalid mining efficiency: expected >= 0, got $efficiency");
        }

        $base /= $efficiency;

        return $base;
    }

	public function canBeBrokenWith(Item $item){
		return $this->getHardness() !== -1;
	}

	/**
	 * Returns the Block on the side $side, works like Vector3::side()
	 *
	 * @param int $side
	 * @param int $step
	 *
	 * @return Block
	 */
	public function getSide($side, $step = 1){
		if($this->isValid()){
			return $this->getLevel()->getBlock(Vector3::getSide($side, $step));
		}

		return Block::get(Block::AIR, 0, Position::fromObject(Vector3::getSide($side, $step)));
	}

	/**
	 * Returns the 4 blocks on the horizontal axes around the block (north, south, east, west)
	 *
	 * @return Block[]
	 */
	public function getHorizontalSides() : array{
		return [
			$this->getSide(Vector3::SIDE_NORTH),
			$this->getSide(Vector3::SIDE_SOUTH),
			$this->getSide(Vector3::SIDE_WEST),
			$this->getSide(Vector3::SIDE_EAST)
		];
	}
	/**
	 * Returns the six blocks around this block.
	 *
	 * @return Block[]
	 */
	public function getAllSides() : array{
		return array_merge(
			[
				$this->getSide(Vector3::SIDE_DOWN),
				$this->getSide(Vector3::SIDE_UP)
			],
			$this->getHorizontalSides()
		);
	}

	/**
	 * Returns a list of blocks that this block is part of. In most cases, only contains the block itself, but in cases
	 * such as double plants, beds and doors, will contain both halves.
	 *
	 * @return Block[]
	 */
	public function getAffectedBlocks() : array{
		return [$this];
	}

	/**
	 * @return string
	 */
	public function __toString(){
		return "Block[" . $this->getName() . "] (" . $this->getId() . ":" . $this->getDamage() . ")";
	}

	/**
	 * Checks for collision against an AxisAlignedBB
	 *
	 * @param AxisAlignedBB $bb
	 *
	 * @return bool
	 */
 	public function collidesWithBB(AxisAlignedBB $bb) : bool{
		$bbs = $this->getCollisionBoxes();

		foreach($bbs as $bb2){
			if($bb->intersectsWith($bb2)){
				return true;
			}
		}
 
		return false;
 	}

	/**
	 * @param Entity $entity
	 */
	public function onEntityCollide(Entity $entity){

	}

	/**
	 * @return AxisAlignedBB[]
	 */
	public function getCollisionBoxes() : array{
		if($this->collisionBoxes === null){
			$this->collisionBoxes = $this->recalculateCollisionBoxes();
		}

		return $this->collisionBoxes;
	}

	/**
	 * @return AxisAlignedBB[]
	 */
	protected function recalculateCollisionBoxes() : array{
		if($this->canPassThrough()){
			return [];
		}

		if($bb = $this->recalculateBoundingBox()){
			return [$bb];
		}

		return [];
	}

	/**
	 * @return AxisAlignedBB|null
	 */
	public function getBoundingBox(){
		if($this->boundingBox === null){
			$this->boundingBox = $this->recalculateBoundingBox();
		}
		return $this->boundingBox;
	}

	/**
	 * @return AxisAlignedBB|null
	 */
	protected function recalculateBoundingBox(){
		if($this->x === null || $this->y === null || $this->z === null){
			return null;
		}
		return new AxisAlignedBB(
			$this->x,
			$this->y,
			$this->z,
			$this->x + 1,
			$this->y + 1,
			$this->z + 1
		);
	}

	/**
	 * Clears any cached precomputed objects, such as bounding boxes. This is called on block neighbour update and when
	 * the block is set into the world to remove any outdated precomputed things such as AABBs and force recalculation.
	 */
	public function clearCaches() : void{
		$this->boundingBox = null;
		$this->collisionBoxes = null;
	}

	/**
	 * @param Vector3 $pos1
	 * @param Vector3 $pos2
	 *
	 * @return RayTraceResult|null
	 */
	public function calculateIntercept(Vector3 $pos1, Vector3 $pos2) : ?RayTraceResult{
		$bbs = $this->getCollisionBoxes();
		if(empty($bbs)){
 			return null;
 		}
 
		/** @var RayTraceResult|null $currentHit */
		$currentHit = null;
		/** @var int|float $currentDistance */
		$currentDistance = PHP_INT_MAX;

		foreach($bbs as $bb){
			$nextHit = $bb->calculateIntercept($pos1, $pos2);
			if($nextHit === null){
				continue;
			}

			$nextDistance = $nextHit->hitVector->distanceSquared($pos1);
			if($nextDistance < $currentDistance){
				$currentHit = $nextHit;
				$currentDistance = $nextDistance;
			}
		}

		if($currentHit !== null){
			$currentHit->blockX = $this->x;
			$currentHit->blockY = $this->y;
			$currentHit->blockZ = $this->z;
 		}
 
		return $currentHit;
	}

	public function setMetadata(string $metadataKey, MetadataValue $newMetadataValue){
		if($this->getLevel() instanceof Level){
			$this->getLevel()->getBlockMetadata()->setMetadata($this, $metadataKey, $newMetadataValue);
		}
	}

	public function getMetadata(string $metadataKey){
		if($this->getLevel() instanceof Level){
			return $this->getLevel()->getBlockMetadata()->getMetadata($this, $metadataKey);
		}

		return null;
	}

	public function hasMetadata(string $metadataKey) : bool{
		if($this->getLevel() instanceof Level){
			return $this->getLevel()->getBlockMetadata()->hasMetadata($this, $metadataKey);
		}

		return false;
	}

	public function removeMetadata(string $metadataKey, Plugin $owningPlugin){
		if($this->getLevel() instanceof Level){
			$this->getLevel()->getBlockMetadata()->removeMetadata($this, $metadataKey, $owningPlugin);
		}
	}
}
