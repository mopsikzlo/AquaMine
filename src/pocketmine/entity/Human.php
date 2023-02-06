<?php

declare(strict_types=1);

namespace pocketmine\entity;

use pocketmine\BedrockPlayer;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\inventory\EnderChestInventory;
use pocketmine\inventory\FloatingInventory;
use pocketmine\inventory\InventoryHolder;
use pocketmine\inventory\PlayerInventory;
use pocketmine\inventory\SimpleTransactionQueue;
use pocketmine\item\Armor;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item as ItemItem;
use pocketmine\item\Totem;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\network\bedrock\protocol\PlayerListPacket as BedrockPlayerListPacket;
use pocketmine\network\bedrock\protocol\PlayerSkinPacket;
use pocketmine\network\bedrock\protocol\types\inventory\ItemInstance;
use pocketmine\network\bedrock\protocol\types\PlayerListEntry as BedrockPlayerListEntry;
use pocketmine\network\mcpe\protocol\AddPlayerPacket;
use pocketmine\network\mcpe\protocol\EntityEventPacket;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\MovePlayerPacket;
use pocketmine\network\mcpe\protocol\PlayerListPacket as McpePlayerListPacket;
use pocketmine\network\mcpe\protocol\types\PlayerListEntry as McpePlayerListEntry;
use pocketmine\network\mcpe\protocol\types\Skin as McpeSkin;
use pocketmine\Player;
use pocketmine\utils\UUID;
use function array_values;
use function floor;
use function max;
use function min;
use function mt_rand;

class Human extends Creature implements ProjectileSource, InventoryHolder{

	public const DATA_PLAYER_FLAG_SLEEP = 1;
	public const DATA_PLAYER_FLAG_DEAD = 2; //TODO: CHECK

	public const DATA_PLAYER_FLAGS = 27;
	public const DATA_PLAYER_INDEX = 28;
	public const DATA_PLAYER_BED_POSITION = 29;

	/** @var PlayerInventory */
	protected $inventory;

	/** @var EnderChestInventory */
	protected $enderChestInventory;

	/** @var FloatingInventory */
	protected $floatingInventory;

	/** @var SimpleTransactionQueue */
	protected $transactionQueue = null;

	/** @var UUID */
	protected $uuid;
	protected $rawUUID;

	public $width = 0.6;
	public $height = 1.8;
	public $eyeHeight = 1.62;

	/** @var Skin */
	protected $skin;

	protected $foodTickTimer = 0;

	protected $totalXp = 0;
	protected $xpSeed;

	protected $baseOffset = 1.62;

	/**
	 * @return UUID|null
	 */
	public function getUniqueId(){
		return $this->uuid;
	}

	/**
	 * @return string
	 */
	public function getRawUniqueId() : string{
		return $this->rawUUID;
	}

	/**
	 * @return Skin
	 */
	public function getSkin() : Skin{
		return $this->skin;
	}

	/**
	 * @param Skin $skin
	 */
	public function setSkin(Skin $skin) : void{
		if(!$skin->isValid()){
			throw new \InvalidArgumentException("Invalid skin given");
		}
		$this->skin = $skin;
	}

	/**
	 * @param Player|Player[]|null $target
	 */
	public function sendSkin($target = null){
		$target = $target ?? $this->hasSpawned;
		if($target instanceof Player){
			$target = [$target];
		}

		$pk = new PlayerSkinPacket();
		$pk->uuid = $this->getUniqueId();
		$pk->skin = $this->skin->getBedrockSkin();

		foreach($target as $player){
			if($player instanceof BedrockPlayer){
				$player->sendDataPacket($pk);
			}elseif($player !== $this){
				// PW10 players need a respawn to update skin
				$this->despawnFrom($player, false);
				$this->spawnTo($player);
			}
		}
	}

	public function jump(){
		parent::jump();
		if($this->isSprinting()){
			$this->exhaust(0.8, PlayerExhaustEvent::CAUSE_SPRINT_JUMPING);
		}else{
			$this->exhaust(0.2, PlayerExhaustEvent::CAUSE_JUMPING);
		}
	}

	public function getFood() : float{
		return $this->attributeMap->getAttribute(Attribute::HUNGER)->getValue();
	}

	/**
	 * WARNING: This method does not check if full and may throw an exception if out of bounds.
	 * Use {@link Human::addFood()} for this purpose
	 *
	 * @param float $new
	 *
	 * @throws \InvalidArgumentException
	 */
	public function setFood(float $new){
		$attr = $this->attributeMap->getAttribute(Attribute::HUNGER);
		$old = $attr->getValue();
		$attr->setValue($new);

		$reset = false;
		// ranges: 18-20 (regen), 7-17 (none), 1-6 (no sprint), 0 (health depletion)
		foreach([17, 6, 0] as $bound){
			if(($old > $bound) !== ($new > $bound)){
				$reset = true;
				break;
			}
		}
		if($reset){
			$this->foodTickTimer = 0;
		}

	}

	public function getMaxFood() : float{
		return $this->attributeMap->getAttribute(Attribute::HUNGER)->getMaxValue();
	}

	public function addFood(float $amount){
		$attr = $this->attributeMap->getAttribute(Attribute::HUNGER);
		$amount += $attr->getValue();
		$amount = max(min($amount, $attr->getMaxValue()), $attr->getMinValue());
		$this->setFood($amount);
	}

	public function getSaturation() : float{
		return $this->attributeMap->getAttribute(Attribute::SATURATION)->getValue();
	}

	/**
	 * WARNING: This method does not check if saturated and may throw an exception if out of bounds.
	 * Use {@link Human::addSaturation()} for this purpose
	 *
	 * @param float $saturation
	 *
	 * @throws \InvalidArgumentException
	 */
	public function setSaturation(float $saturation){
		$this->attributeMap->getAttribute(Attribute::SATURATION)->setValue($saturation);
	}

	public function addSaturation(float $amount){
		$attr = $this->attributeMap->getAttribute(Attribute::SATURATION);
		$attr->setValue($attr->getValue() + $amount, true);
	}

	public function getExhaustion() : float{
		return $this->attributeMap->getAttribute(Attribute::EXHAUSTION)->getValue();
	}

	/**
	 * WARNING: This method does not check if exhausted and does not consume saturation/food.
	 * Use {@link Human::exhaust()} for this purpose.
	 *
	 * @param float $exhaustion
	 */
	public function setExhaustion(float $exhaustion){
		$this->attributeMap->getAttribute(Attribute::EXHAUSTION)->setValue($exhaustion);
	}

	/**
	 * Increases a human's exhaustion level.
	 *
	 * @param float $amount
	 * @param int   $cause
	 *
	 * @return float the amount of exhaustion level increased
	 */
	public function exhaust(float $amount, int $cause = PlayerExhaustEvent::CAUSE_CUSTOM) : float{
		$ev = new PlayerExhaustEvent($this, $amount, $cause);
		$ev->call();
		if($ev->isCancelled()){
			return 0.0;
		}

		$exhaustion = $this->getExhaustion();
		$exhaustion += $ev->getAmount();

		while($exhaustion >= 4.0){
			$exhaustion -= 4.0;

			$saturation = $this->getSaturation();
			if($saturation > 0){
				$saturation = max(0, $saturation - 1.0);
				$this->setSaturation($saturation);
			}else{
				$food = $this->getFood();
				if($food > 0){
					$food--;
					$this->setFood($food);
				}
			}
		}
		$this->setExhaustion($exhaustion);

		return $ev->getAmount();
	}

	public function getXpLevel() : int{
		return (int) $this->attributeMap->getAttribute(Attribute::EXPERIENCE_LEVEL)->getValue();
	}

	public function setXpLevel(int $level){
		$this->attributeMap->getAttribute(Attribute::EXPERIENCE_LEVEL)->setValue($level);
	}

	public function getXpProgress() : float{
		return $this->attributeMap->getAttribute(Attribute::EXPERIENCE)->getValue();
	}

	public function setXpProgress(float $progress){
		$this->attributeMap->getAttribute(Attribute::EXPERIENCE)->setValue($progress);
	}

	public function getTotalXp() : int{
		return $this->totalXp;
	}

	public function getRemainderXp() : int{
		return $this->getTotalXp() - self::getTotalXpForLevel($this->getXpLevel());
	}

	public function recalculateXpProgress() : float{
		$this->setXpProgress($progress = $this->getRemainderXp() / self::getTotalXpForLevel($this->getXpLevel()));
		return $progress;
	}

	public static function getTotalXpForLevel(int $level) : int{
		if($level <= 16){
			return $level ** 2 + $level * 6;
		}elseif($level < 32){
			return $level ** 2 * 2.5 - 40.5 * $level + 360;
		}
		return $level ** 2 * 4.5 - 162.5 * $level + 2220;
	}

	public function getInventory(){
		return $this->inventory;
	}

	public function getEnderChestInventory() : EnderChestInventory{
		return $this->enderChestInventory;
	}

	public function getFloatingInventory(){
		return $this->floatingInventory;
	}

	public function getTransactionQueue(){
		//Is creating the transaction queue ondemand a good idea? I think only if it's destroyed afterwards. hmm...
		if($this->transactionQueue === null){
			//Potential for crashes here if a plugin attempts to use this, say for an NPC plugin or something...
			$this->transactionQueue = new SimpleTransactionQueue($this);
		}
		return $this->transactionQueue;
 	}

	protected function initEntity(){

		$this->setDataFlag(self::DATA_PLAYER_FLAGS, self::DATA_PLAYER_FLAG_SLEEP, false, self::DATA_TYPE_BYTE);
		$this->setDataProperty(self::DATA_PLAYER_BED_POSITION, self::DATA_TYPE_POS, [0, 0, 0], false);

		$inventoryContents = $this->namedtag->getListTag("Inventory");
		$this->inventory = new PlayerInventory($this, $inventoryContents);
		$this->enderChestInventory = new EnderChestInventory($this);

		//Virtual inventory for desktop GUI crafting and anti-cheat transaction processing
		$this->floatingInventory = new FloatingInventory($this);

		if($this instanceof Player){
			$this->addWindow($this->inventory, 0);
		}else{
			if($this->namedtag->hasTag("NameTag", StringTag::class)){
				$this->setNameTag($this->namedtag->getString("NameTag"));
			}

			if($this->namedtag->hasTag("Skin", CompoundTag::class)){
				$skinTag = $this->namedtag->getCompoundTag("Skin");
				$this->setSkin(Skin::fromMcpeSkin(new McpeSkin($skinTag->getString("Name"), $skinTag->getString("Data"))));
			}else{
				throw new \InvalidStateException((new \ReflectionClass($this))->getShortName() . " must have a valid skin set");
			}

			$this->uuid = UUID::fromData((string) $this->getId(), $this->skin->getMcpeSkin()->getSkinData(), $this->getNameTag());
		}

		if($this->namedtag->hasTag("EnderChestInventory", ListTag::class)){
			$itemList = $this->namedtag->getListTag("EnderChestInventory");
			if($itemList->getTagType() === NBT::TAG_Compound){
				foreach($itemList as $item){
					/** @var CompoundTag $item */
					$this->enderChestInventory->setItem($item->getByte("Slot"), ItemItem::nbtDeserialize($item), false);
				}
			}
		}

		if($this->namedtag->hasTag("SelectedInventorySlot", IntTag::class)){
			$this->inventory->setHeldItemSlot($this->namedtag->getInt("SelectedInventorySlot"), false);
		}else{
			$this->inventory->setHeldItemSlot(0, false);
		}

		parent::initEntity();

		if(!$this->namedtag->hasTag("foodLevel", IntTag::class)){
			$this->namedtag->setInt("foodLevel", (int) $this->getFood());
		}else{
			$this->setFood((float) $this->namedtag->getInt("foodLevel"));
		}

		if(!$this->namedtag->hasTag("foodExhaustionLevel", FloatTag::class)){
			$this->namedtag->setFloat("foodExhaustionLevel", $this->getExhaustion());
		}else{
			$this->setExhaustion($this->namedtag->getFloat("foodExhaustionLevel"));
		}

		if(!$this->namedtag->hasTag("foodSaturationLevel", FloatTag::class)){
			$this->namedtag->setFloat("foodSaturationLevel", $this->getSaturation());
		}else{
			$this->setSaturation($this->namedtag->getFloat("foodSaturationLevel"));
		}

		if(!$this->namedtag->hasTag("foodTickTimer", IntTag::class)){
			$this->namedtag->setInt("foodTickTimer", $this->foodTickTimer);
		}else{
			$this->foodTickTimer = $this->namedtag->getInt("foodTickTimer");
		}

		if(!$this->namedtag->hasTag("XpLevel", IntTag::class)){
			$this->namedtag->setInt("XpLevel", $this->getXpLevel());
		}else{
			$this->setXpLevel($this->namedtag->getInt("XpLevel"));
		}

		if(!$this->namedtag->hasTag("XpP", FloatTag::class)){
			$this->namedtag->setFloat("XpP", $this->getXpProgress());
		}

		if(!$this->namedtag->hasTag("XpTotal", IntTag::class)){
			$this->namedtag->setInt("XpTotal", $this->totalXp);
		}else{
			$this->totalXp = $this->namedtag->getInt("XpTotal");
		}

		if(!$this->namedtag->hasTag("XpSeed", IntTag::class)){
			$this->namedtag->setInt("XpSeed", $this->xpSeed ?? ($this->xpSeed = mt_rand(-0x80000000, 0x7fffffff)));
		}else{
			$this->xpSeed = $this->namedtag->getInt("XpSeed");
		}
	}

	protected function addAttributes(){
		parent::addAttributes();

		$this->attributeMap->addAttribute(Attribute::getAttribute(Attribute::SATURATION));
		$this->attributeMap->addAttribute(Attribute::getAttribute(Attribute::EXHAUSTION));
		$this->attributeMap->addAttribute(Attribute::getAttribute(Attribute::HUNGER));
		$this->attributeMap->addAttribute(Attribute::getAttribute(Attribute::EXPERIENCE_LEVEL));
		$this->attributeMap->addAttribute(Attribute::getAttribute(Attribute::EXPERIENCE));
	}

	public function entityBaseTick($tickDiff = 1){
		$hasUpdate = parent::entityBaseTick($tickDiff);

		$this->doFoodTick($tickDiff);

		return $hasUpdate;
	}

	public function doFoodTick(int $tickDiff = 1){
		if($this->isAlive()){
			$food = $this->getFood();
			$health = $this->getHealth();
			$difficulty = $this->server->getDifficulty();

			$this->foodTickTimer += $tickDiff;
			if($this->foodTickTimer >= 80){
				$this->foodTickTimer = 0;
			}

			if($difficulty === 0 and $this->foodTickTimer % 10 === 0){ //Peaceful
				if($food < 20){
					$this->addFood(1.0);
				}
				if($this->foodTickTimer % 20 === 0 and $health < $this->getMaxHealth()){
					$this->heal(1, new EntityRegainHealthEvent($this, 1, EntityRegainHealthEvent::CAUSE_SATURATION));
				}
			}

			if($this->foodTickTimer === 0){
				if($food >= 18){
					if($health < $this->getMaxHealth()){
						$this->heal(1, new EntityRegainHealthEvent($this, 1, EntityRegainHealthEvent::CAUSE_SATURATION));
						$this->exhaust(3.0, PlayerExhaustEvent::CAUSE_HEALTH_REGEN);
					}
				}elseif($food <= 0){
					if(($difficulty === 1 and $health > 10) or ($difficulty === 2 and $health > 1) or $difficulty === 3){
						$this->attack(1, new EntityDamageEvent($this, EntityDamageEvent::CAUSE_STARVATION, 1));
					}
				}
			}

			if($food <= 6){
				if($this->isSprinting()){
					$this->setSprinting(false);
				}
			}
		}
	}

	public function getName(){
		return $this->getNameTag();
	}

	public function applyDamageModifiers(EntityDamageEvent $source) : void{
		parent::applyDamageModifiers($source);

		$type = $source->getCause();

		$armorPoints = 0;
		$enchantments = 0; //Armor enchantments
		foreach($this->inventory->getArmorContents() as $i){
			$armorPoints += $i->getArmorPoints();
			if(($enchantment = $i->getEnchantment(Enchantment::PROTECTION)) !== null){
				$enchantments += (int) floor((6 + $enchantment->getLevel() ** 2) * 0.25);
			}
			if($type === EntityDamageEvent::CAUSE_PROJECTILE and ($enchantment = $i->getEnchantment(Enchantment::PROJECTILE_PROTECTION)) !== null){
				$enchantments += (int) floor((6 + $enchantment->getLevel() ** 2) * 0.5);
			}
		}
		if($source->canBeReducedByArmor()){
			$source->setDamage(-$source->getFinalDamage() * $armorPoints * 0.04, EntityDamageEvent::MODIFIER_ARMOR);
		}

		if($type !== EntityDamageEvent::CAUSE_VOID and $type !== EntityDamageEvent::CAUSE_STARVATION and $type !== EntityDamageEvent::CAUSE_SUICIDE){
			$source->setDamage(-$source->getFinalDamage() * $enchantments * 0.04, EntityDamageEvent::MODIFIER_ENCHANTMENT_PROTECTION);
		}

		$feet = $this->inventory->getBoots();
		if($type === EntityDamageEvent::CAUSE_FALL and ($enchantment = $feet->getEnchantment(Enchantment::FEATHER_FALLING)) !== null){
			$reduction = 2.0 * $enchantment->getLevel();
			if($reduction > $source->getFinalDamage()){
				$reduction = $source->getFinalDamage();
			}
			$source->setDamage(-$reduction, EntityDamageEvent::MODIFIER_ENCHANTMENT_FEATHER_FALLING);
		}

		if($type !== EntityDamageEvent::CAUSE_SUICIDE and $type !== EntityDamageEvent::CAUSE_VOID and $this->inventory->getOffHand() instanceof Totem){
			$compensation = $this->getHealth() - $source->getFinalDamage() - 1;
			if($compensation < 0){
				$source->setDamage($compensation, EntityDamageEvent::MODIFIER_TOTEM);
			}
		}
	}

	protected function applyPostDamageEffects(EntityDamageEvent $source) : void{
		parent::applyPostDamageEffects($source);

		$totemModifier = $source->getDamage(EntityDamageEvent::MODIFIER_TOTEM);
		if($totemModifier < 0){ //Totem prevented death
			$this->removeAllEffects();

			$this->addEffect(Effect::getEffect(Effect::REGENERATION)->setDuration(40 * 20)->setAmplifier(1));
			$this->addEffect(Effect::getEffect(Effect::FIRE_RESISTANCE)->setDuration(40 * 20)->setAmplifier(1));
			$this->addEffect(Effect::getEffect(Effect::ABSORPTION)->setDuration(5 * 20)->setAmplifier(1));

			$this->broadcastEntityEvent(EntityEventPacket::CONSUME_TOTEM);
			if($this instanceof Player){
				$this->broadcastEntityEvent(EntityEventPacket::CONSUME_TOTEM, null, [$this]);
			}
			$this->level->broadcastLevelEvent($this->add(0, $this->eyeHeight, 0), LevelEventPacket::EVENT_SOUND_TOTEM);

			$offhand = $this->inventory->getOffHand();
			if($offhand instanceof Totem){
				--$offhand->count;
				if($offhand->count === 0){
					$this->inventory->clear(PlayerInventory::OFFHAND_INDEX);
				}else{
					$this->inventory->setOffHand($offhand);
				}
			}
		}

		$this->damageArmor($source->getDamage(EntityDamageEvent::MODIFIER_BASE));
	}
	/**
	 * Damages the worn armour according to the amount of damage given. Each 4 points (rounded down) deals 1 damage
	 * point to each armour piece, but never less than 1 total.
	 *
	 * @param float $damage
	 */
	public function damageArmor(float $damage) : void{
		$durabilityRemoved = (int) max(floor($damage / 4), 1);
		$armor = $this->inventory->getArmorContents();
		foreach($armor as $slot => $item){
			if($item instanceof Armor){
				$item->applyDamage($durabilityRemoved);

				if($item->isBroken()){
					$this->level->broadcastLevelSoundEvent($this, LevelSoundEventPacket::SOUND_BREAK);
				}
			}
		}
		$this->inventory->setArmorContents($armor);
	}

	public function getDrops() : array{
		return $this->inventory !== null ? array_values($this->inventory->getContents()) : [];
	}

	public function saveNBT(){
		parent::saveNBT();

		$this->namedtag->setInt("foodLevel", (int) $this->getFood());
		$this->namedtag->setFloat("foodExhaustionLevel", $this->getExhaustion());
		$this->namedtag->setFloat("foodSaturationLevel", $this->getSaturation());
		$this->namedtag->setInt("foodTickTimer", $this->foodTickTimer);

		$this->namedtag->setTag("Inventory", $inventoryTag = new ListTag([], NBT::TAG_Compound));
		if($this->inventory !== null){
			//Normal inventory
			$slotCount = $this->inventory->getSize();
			for($slot = 0; $slot < $slotCount; ++$slot){
				$item = $this->inventory->getItem($slot);
				if($item->getId() !== ItemItem::AIR){
					$inventoryTag->push($item->nbtSerialize($slot));
				}
			}

			//Armor and offhand
			for($slot = 100; $slot < 105; ++$slot){
				$item = $this->inventory->getItem($this->inventory->getSize() + $slot - 100);
				if($item instanceof ItemItem and $item->getId() !== ItemItem::AIR){
					$inventoryTag->push($item->nbtSerialize($slot));
				}
			}

			$this->namedtag->setInt("SelectedInventorySlot", $this->inventory->getHeldItemSlot());
		}

		if($this->enderChestInventory !== null){
			/** @var CompoundTag[] $items */
			$items = [];

			$slotCount = $this->enderChestInventory->getSize();
			for($slot = 0; $slot < $slotCount; ++$slot){
				$item = $this->enderChestInventory->getItem($slot);
				if(!$item->isNull()){
					$items[] = $item->nbtSerialize($slot);
				}
			}

			$this->namedtag->setTag("EnderChestInventory", new ListTag($items));
		}

		// TODO: saving in bedrock format?
		$this->namedtag->setTag("Skin", CompoundTag::create()
			->setString("Data", $this->skin->getMcpeSkin()->getSkinData())
			->setString("Name", $this->skin->getMcpeSkin()->getSkinId()));
	}

	public function spawnTo(Player $player) : void{
		if($player !== $this){
			parent::spawnTo($player);
		}
	}

	protected function sendSpawnPacket(Player $player) : void{
		$packets = [];

		if(!($this instanceof Player)){
			if($player instanceof BedrockPlayer){
				$pk = new BedrockPlayerListPacket();
				$pk->type = BedrockPlayerListPacket::TYPE_ADD;
				$pk->entries[] = BedrockPlayerListEntry::createAdditionEntry($this->getUniqueId(), $this->getId(), $this->getName(), $this->skin->getBedrockSkin());
			}else{
				$pk = new McpePlayerListPacket();
				$pk->type = McpePlayerListPacket::TYPE_ADD;
				$pk->entries[] = McpePlayerListEntry::createAdditionEntry($this->getUniqueId(), $this->getId(), $this->getName(), $this->skin->getMcpeSkin());
			}
			$packets[] = $pk;
		}

		$pk = new AddPlayerPacket();
		$pk->uuid = $this->getUniqueId();
		$pk->username = $this->getName();
		$pk->entityRuntimeId = $this->getId();
		$pk->x = $this->x;
		$pk->y = $this->y;
		$pk->z = $this->z;
		$pk->speedX = $this->motionX;
		$pk->speedY = $this->motionY;
		$pk->speedZ = $this->motionZ;
		$pk->yaw = $this->yaw;
		$pk->pitch = $this->pitch;
		$pk->item = $this->getInventory()->getItemInHand();
		$pk->metadata = $this->dataProperties;
		$packets[] = $pk;

		if(!($this instanceof Player)){
			if($player instanceof BedrockPlayer){
				$pk = new BedrockPlayerListPacket();
				$pk->type = BedrockPlayerListPacket::TYPE_REMOVE;
				$pk->entries[] = BedrockPlayerListEntry::createRemovalEntry($this->getUniqueId());
			}else{
				$pk = new McpePlayerListPacket();
				$pk->type = McpePlayerListPacket::TYPE_REMOVE;
				$pk->entries[] = McpePlayerListEntry::createRemovalEntry($this->getUniqueId());
			}
			$packets[] = $pk;
		}
		$this->server->batchPackets([$player], $packets, true);

		if($player instanceof BedrockPlayer){
			//TODO: Hack for MCPE 1.2.13+: DATA_NAMETAG is useless in AddPlayerPacket, so it has to be sent separately
			$this->sendData($player, [self::DATA_NAMETAG => [self::DATA_TYPE_STRING, $this->getNameTag()]]);
		}

		$this->inventory->sendArmorContents($player);
	}

	public function close(){
		if(!$this->closed){
			if($this->getFloatingInventory() instanceof FloatingInventory){
 				foreach($this->getFloatingInventory()->getContents() as $craftingItem){
 					$this->getInventory()->addItem($craftingItem);
					$this->getFloatingInventory()->removeItem($craftingItem);
 				}
 			}else{
 				$this->server->getLogger()->debug("Attempted to drop a null crafting inventory\n");
 			}
			if($this->inventory !== null){
				foreach($this->inventory->getViewers() as $viewer){
					$viewer->removeWindow($this->inventory);
				}
			}
			if($this->enderChestInventory !== null){
				foreach($this->enderChestInventory->getViewers() as $viewer){
					$viewer->removeWindow($this->enderChestInventory);
				}
			}
			parent::close();
		}
	}
}