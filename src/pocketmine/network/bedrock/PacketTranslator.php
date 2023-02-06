<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock;

use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\network\bedrock\palette\ActorMapping;
use pocketmine\network\bedrock\palette\BlockPalette;
use pocketmine\network\bedrock\protocol\ActorEventPacket as BedrockActorEventPacket;
use pocketmine\network\bedrock\protocol\AddActorPacket as BedrockAddActorPacket;
use pocketmine\network\bedrock\protocol\AddItemActorPacket as BedrockAddItemActorPacket;
use pocketmine\network\bedrock\protocol\AddPlayerPacket as BedrockAddPlayerPacket;
use pocketmine\network\bedrock\protocol\AnimatePacket as BedrockAnimatePacket;
use pocketmine\network\bedrock\protocol\BlockActorDataPacket as BedrockBlockActorDataPacket;
use pocketmine\network\bedrock\protocol\BlockEventPacket as BedrockBlockEventPacket;
use pocketmine\network\bedrock\protocol\BossEventPacket as BedrockBossEventPacket;
use pocketmine\network\bedrock\protocol\ChangeDimensionPacket as BedrockChangeDimensionPacket;
use pocketmine\network\bedrock\protocol\ClientboundMapItemDataPacket as BedrockClientboundMapItemDataPacket;
use pocketmine\network\bedrock\protocol\ContainerClosePacket as BedrockContainerClosePacket;
use pocketmine\network\bedrock\protocol\ContainerOpenPacket as BedrockContainerOpenPacket;
use pocketmine\network\bedrock\protocol\ContainerSetDataPacket as BedrockContainerSetDataPacket;
use pocketmine\network\bedrock\protocol\CraftingDataPacket as BedrockCraftingDataPacket;
use pocketmine\network\bedrock\protocol\DataPacket as BedrockPacket;
use pocketmine\network\bedrock\protocol\InventoryContentPacket as BedrockInventoryContentPacket;
use pocketmine\network\bedrock\protocol\InventorySlotPacket as BedrockInventorySlotPacket;
use pocketmine\network\bedrock\protocol\LevelEventPacket as BedrockLevelEventPacket;
use pocketmine\network\bedrock\protocol\LevelSoundEventPacket as BedrockLevelSoundEventPacket;
use pocketmine\network\bedrock\protocol\MobArmorEquipmentPacket as BedrockMobArmorEquipmentPacket;
use pocketmine\network\bedrock\protocol\MobEffectPacket as BedrockMobEffectPacket;
use pocketmine\network\bedrock\protocol\MobEquipmentPacket as BedrockMobEquipmentPacket;
use pocketmine\network\bedrock\protocol\MoveActorAbsolutePacket as BedrockMoveActorAbsolutePacket;
use pocketmine\network\bedrock\protocol\MovePlayerPacket as BedrockMovePlayerPacket;
use pocketmine\network\bedrock\protocol\PlayerListPacket as BedrockPlayerListPacket;
use pocketmine\network\bedrock\protocol\PlaySoundPacket as BedrockPlaySoundPacket;
use pocketmine\network\bedrock\protocol\RemoveActorPacket as BedrockRemoveActorPacket;
use pocketmine\network\bedrock\protocol\RespawnPacket as BedrockRespawnPacket;
use pocketmine\network\bedrock\protocol\SetActorDataPacket as BedrockSetActorDataPacket;
use pocketmine\network\bedrock\protocol\SetActorLinkPacket as BedrockSetActorLinkPacket;
use pocketmine\network\bedrock\protocol\SetActorMotionPacket as BedrockSetActorMotionPacket;
use pocketmine\network\bedrock\protocol\SetDifficultyPacket as BedrockSetDifficultyPacket;
use pocketmine\network\bedrock\protocol\SetPlayerGameTypePacket as BedrockSetPlayerGameTypePacket;
use pocketmine\network\bedrock\protocol\SetSpawnPositionPacket as BedrockSetSpawnPositionPacket;
use pocketmine\network\bedrock\protocol\SetTimePacket as BedrockSetTimePacket;
use pocketmine\network\bedrock\protocol\SpawnParticleEffectPacket;
use pocketmine\network\bedrock\protocol\StopSoundPacket as BedrockStopSoundPacket;
use pocketmine\network\bedrock\protocol\TakeItemActorPacket as BedrockTakeItemActorPacket;
use pocketmine\network\bedrock\protocol\TextPacket as BedrockTextPacket;
use pocketmine\network\bedrock\protocol\types\actor\ActorLink;
use pocketmine\network\bedrock\protocol\types\actor\ActorMetadataFlags;
use pocketmine\network\bedrock\protocol\types\actor\ActorMetadataProperties;
use pocketmine\network\bedrock\protocol\types\actor\ActorMetadataTypes;
use pocketmine\network\bedrock\protocol\types\inventory\ItemInstance;
use pocketmine\network\bedrock\protocol\types\LevelEventParticleIds;
use pocketmine\network\bedrock\protocol\types\ParticleEffectIds;
use pocketmine\network\bedrock\protocol\types\PlayerListEntry as BedrockPlayerListEntry;
use pocketmine\network\bedrock\protocol\UpdateAttributesPacket as BedrockUpdateAttributesPacket;
use pocketmine\network\bedrock\protocol\UpdateBlockPacket as BedrockUpdateBlockPacket;
use pocketmine\network\bedrock\skin\SkinConverter;
use pocketmine\network\mcpe\protocol\AddEntityPacket as MCPEAddEntityPacket;
use pocketmine\network\mcpe\protocol\AddItemEntityPacket as MCPEAddItemEntityPacket;
use pocketmine\network\mcpe\protocol\AddPlayerPacket as MCPEAddPlayerPacket;
use pocketmine\network\mcpe\protocol\AnimatePacket as MCPEAnimatePacket;
use pocketmine\network\mcpe\protocol\BlockEntityDataPacket as MCPEBlockEntityDataPacket;
use pocketmine\network\mcpe\protocol\BlockEventPacket as MCPEBlockEventPacket;
use pocketmine\network\mcpe\protocol\BossEventPacket as MCPEBossEventPacket;
use pocketmine\network\mcpe\protocol\ChangeDimensionPacket as MCPEChangeDimensionPacket;
use pocketmine\network\mcpe\protocol\ClientboundMapItemDataPacket as MCPEClientboundMapItemDataPacket;
use pocketmine\network\mcpe\protocol\ContainerClosePacket as MCPEContainerClosePacket;
use pocketmine\network\mcpe\protocol\ContainerOpenPacket as MCPEContainerOpenPacket;
use pocketmine\network\mcpe\protocol\ContainerSetContentPacket as MCPEContainerSetContentPacket;
use pocketmine\network\mcpe\protocol\ContainerSetDataPacket as MCPEContainerSetDataPacket;
use pocketmine\network\mcpe\protocol\ContainerSetSlotPacket as MCPEContainerSetSlotPacket;
use pocketmine\network\mcpe\protocol\CraftingDataPacket as MCPECraftingDataPacket;
use pocketmine\network\mcpe\protocol\DataPacket as MCPEPacket;
use pocketmine\network\mcpe\protocol\EntityEventPacket;
use pocketmine\network\mcpe\protocol\EntityEventPacket as MCPEEntityEventPacket;
use pocketmine\network\mcpe\protocol\LevelEventPacket as MCPELevelEventPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket as MCPELevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\MobArmorEquipmentPacket as MCPEMobArmorEquipmentPacket;
use pocketmine\network\mcpe\protocol\MobEffectPacket as MCPEMobEffectPacket;
use pocketmine\network\mcpe\protocol\MobEquipmentPacket as MCPEMobEquipmentPacket;
use pocketmine\network\mcpe\protocol\MoveEntityPacket as MCPEMoveEntityPacket;
use pocketmine\network\mcpe\protocol\MovePlayerPacket as MCPEMovePlayerPacket;
use pocketmine\network\mcpe\protocol\PlayerListPacket as MCPEPlayerListPacket;
use pocketmine\network\mcpe\protocol\PlaySoundPacket as MCPEPlaySoundPacket;
use pocketmine\network\mcpe\protocol\ProtocolInfo as MCPEProtocolInfo;
use pocketmine\network\mcpe\protocol\RemoveEntityPacket as MCPERemoveEntityPacket;
use pocketmine\network\mcpe\protocol\RespawnPacket as MCPERespawnPacket;
use pocketmine\network\mcpe\protocol\SetDifficultyPacket as MCPESetDifficultyPacket;
use pocketmine\network\mcpe\protocol\SetEntityDataPacket as MCPESetEntityDataPacket;
use pocketmine\network\mcpe\protocol\SetEntityLinkPacket as MCPESetEntityLinkPacket;
use pocketmine\network\mcpe\protocol\SetEntityMotionPacket as MCPESetEntityMotionPacket;
use pocketmine\network\mcpe\protocol\SetPlayerGameTypePacket as MCPESetPlayerGameTypePacket;
use pocketmine\network\mcpe\protocol\SetSpawnPositionPacket as MCPESetSpawnPositionPacket;
use pocketmine\network\mcpe\protocol\SetTimePacket as MCPESetTimePacket;
use pocketmine\network\mcpe\protocol\StopSoundPacket as MCPEStopSoundPacket;
use pocketmine\network\mcpe\protocol\TakeItemEntityPacket as MCPETakeItemEntityPacket;
use pocketmine\network\mcpe\protocol\TextPacket as MCPETextPacket;
use pocketmine\network\mcpe\protocol\types\LevelEventParticleIds as MCPELevelEventParticleIds;
use pocketmine\network\mcpe\protocol\UpdateAttributesPacket as MCPEUpdateAttributesPacket;
use pocketmine\network\mcpe\protocol\UpdateBlockPacket as MCPEUpdateBlockPacket;
use function array_map;
use function str_replace;

/**
 * This class is used to translate MCPE 1.1 packets to their Bedrock 1.12 equivalents.
 * However, usage of this is kind of hacky and might lose a lot of performance.
 * TODO: remove this after everything from here will be moved to the network layer.
 */
abstract class PacketTranslator{

	/**
	 * Tries to translate the packet to Bedrock Edition client.
	 *
	 * @param MCPEPacket    $packet
	 *
	 * @return BedrockPacket|null
	 */
	public static function translate(MCPEPacket $packet) : ?BedrockPacket{
		switch($packet->pid()){
			case MCPEProtocolInfo::TEXT_PACKET:
				/** @var MCPETextPacket $packet */
				static $types = [
					MCPETextPacket::TYPE_RAW => BedrockTextPacket::TYPE_RAW,
					MCPETextPacket::TYPE_CHAT => BedrockTextPacket::TYPE_CHAT,
					MCPETextPacket::TYPE_TRANSLATION => BedrockTextPacket::TYPE_TRANSLATION,
					MCPETextPacket::TYPE_POPUP => BedrockTextPacket::TYPE_POPUP,
					MCPETextPacket::TYPE_TIP => BedrockTextPacket::TYPE_TIP,
					MCPETextPacket::TYPE_SYSTEM => BedrockTextPacket::TYPE_SYSTEM,
					MCPETextPacket::TYPE_WHISPER => BedrockTextPacket::TYPE_WHISPER,
					MCPETextPacket::TYPE_ANNOUNCEMENT => BedrockTextPacket::TYPE_ANNOUNCEMENT
				];
				$pk = new BedrockTextPacket();
				$pk->type = $types[$packet->type];
				$pk->needsTranslation = $packet->type === MCPETextPacket::TYPE_TRANSLATION;
				$pk->sourceName = $packet->source;
				$pk->message = str_replace("§r", "§r§f", $packet->message); //color hack
				$pk->parameters = $packet->parameters;
				break;
			case MCPEProtocolInfo::UPDATE_ATTRIBUTES_PACKET:
				/** @var MCPEUpdateAttributesPacket $packet */
				$pk = new BedrockUpdateAttributesPacket();
				$pk->actorRuntimeId = $packet->entityRuntimeId;
				$pk->entries = $packet->entries;
				break;
			case MCPEProtocolInfo::SET_PLAYER_GAME_TYPE_PACKET:
				/** @var MCPESetPlayerGameTypePacket $packet */
				$pk = new BedrockSetPlayerGameTypePacket();
				$pk->gamemode = $packet->gamemode;
				break;
			case MCPEProtocolInfo::SET_TIME_PACKET:
				/** @var MCPESetTimePacket $packet */
				$pk = new BedrockSetTimePacket();
				$pk->time = $packet->time;
				break;
			case MCPEProtocolInfo::MOVE_PLAYER_PACKET:
				/** @var MCPEMovePlayerPacket $packet */
				$pk = new BedrockMovePlayerPacket();
				$pk->actorRuntimeId = $packet->entityRuntimeId;
				$pk->position = new Vector3($packet->x, $packet->y + 0.001 + ($packet->mode === MCPEMovePlayerPacket::MODE_TELEPORT ? 1.62 : 0.0), $packet->z); #BlameMojang
				$pk->pitch = $packet->pitch;
				$pk->yaw = $packet->yaw;
				$pk->headYaw = $packet->bodyYaw;
				$pk->mode = $packet->mode;
				$pk->onGround = $packet->onGround;
				$pk->ridingEid = $packet->ridingEid;
				break;
			case MCPEProtocolInfo::CONTAINER_OPEN_PACKET:
				/** @var MCPEContainerOpenPacket $packet */
				$pk = new BedrockContainerOpenPacket();
				$pk->windowId = $packet->windowId;
				$pk->type = $packet->type; //no translation needed
				$pk->x = $packet->x;
				$pk->y = $packet->y;
				$pk->z = $packet->z;
				$pk->actorUniqueId = $packet->entityUniqueId;
				break;
			case MCPEProtocolInfo::CONTAINER_CLOSE_PACKET:
				/** @var MCPEContainerClosePacket $packet */
				$pk = new BedrockContainerClosePacket();
				$pk->windowId = $packet->windowId;
				$pk->server = true;
				break;
			case MCPEProtocolInfo::CONTAINER_SET_DATA_PACKET:
				/** @var MCPEContainerSetDataPacket $packet */
				$pk = new BedrockContainerSetDataPacket();
				$pk->windowId = $packet->windowId;
				$pk->property = $packet->property;
				$pk->value = $packet->value;
				break;
			case MCPEProtocolInfo::CONTAINER_SET_SLOT_PACKET:
				/** @var MCPEContainerSetSlotPacket $packet */
				$pk = new BedrockInventorySlotPacket();
				$pk->windowId = $packet->windowId;
				$pk->inventorySlot = $packet->slot;
				$pk->item = ItemInstance::legacy($packet->item);
				break;
			case MCPEProtocolInfo::CONTAINER_SET_CONTENT_PACKET:
				/** @var MCPEContainerSetContentPacket $packet */
				$pk = new BedrockInventoryContentPacket();
				$pk->windowId = $packet->windowId;
				$pk->items = array_map([ItemInstance::class, 'legacy'], $packet->slots);
				//ignore hotbar
				break;
			case MCPEProtocolInfo::BLOCK_ENTITY_DATA_PACKET:
				/** @var MCPEBlockEntityDataPacket $packet */
				$pk = new BedrockBlockActorDataPacket();
				$pk->x = $packet->x;
				$pk->y = $packet->y;
				$pk->z = $packet->z;
				$pk->namedtag = $packet->namedtag;
				break;
			case MCPEProtocolInfo::ADD_ENTITY_PACKET:
				/** @var MCPEAddEntityPacket $packet */
				$pk = new BedrockAddActorPacket();
				$pk->actorUniqueId = $packet->entityUniqueId;
				$pk->actorRuntimeId = $packet->entityRuntimeId;
				$pk->type = ActorMapping::getStringIdFromLegacyId($packet->type);
				$pk->position = new Vector3($packet->x, $packet->y, $packet->z);
				$pk->motion = new Vector3($packet->speedX, $packet->speedY, $packet->speedZ);
				$pk->pitch = $packet->pitch;
				$pk->yaw = $pk->headYaw = $packet->yaw;
				$pk->attributes = $packet->attributes;
				$pk->metadata = self::translateMetadata($packet->metadata); //:o
				foreach($packet->links as $link){
					$pk->links[] = new ActorLink($link[0], $link[1], $link[2]);
				}
				break;
			case MCPEProtocolInfo::MOVE_ENTITY_PACKET:
				/** @var MCPEMoveEntityPacket $packet */
				$pk = new BedrockMoveActorAbsolutePacket();
				$pk->actorRuntimeId = $packet->entityRuntimeId;
				$pk->position = new Vector3($packet->x, $packet->y, $packet->z);
				$pk->xRot = $packet->pitch;
				$pk->yRot = $packet->headYaw;
				$pk->zRot = $packet->yaw;
				if($packet->onGround){
					$pk->flags |= BedrockMoveActorAbsolutePacket::FLAG_GROUND;
				}
				if($packet->teleported){
					$pk->flags |= BedrockMoveActorAbsolutePacket::FLAG_TELEPORT;
				}
				break;
			case MCPEProtocolInfo::ADD_PLAYER_PACKET:
				/** @var MCPEAddPlayerPacket $packet */
				$pk = new BedrockAddPlayerPacket();
				$pk->uuid = $packet->uuid;
				$pk->username = $packet->username;
				$pk->actorUniqueId = $packet->entityUniqueId;
				$pk->actorRuntimeId = $packet->entityRuntimeId;
				$pk->position = new Vector3($packet->x, $packet->y, $packet->z);
				$pk->motion = new Vector3($packet->speedX, $packet->speedY, $packet->speedZ);
				$pk->pitch = $packet->pitch;
				$pk->yaw = $packet->yaw;
				$pk->headYaw = $packet->headYaw;
				$pk->item = ItemInstance::legacy($packet->item);
				$pk->metadata = self::translateMetadata($packet->metadata); //:o
				break;
			case MCPEProtocolInfo::PLAYER_LIST_PACKET:
				/** @var MCPEPlayerListPacket $packet */
				$pk = new BedrockPlayerListPacket();
				$pk->type = $packet->type;
				foreach($packet->entries as $mcpeEntry){
					$entry = new BedrockPlayerListEntry();
					$entry->uuid = $mcpeEntry->uuid;
					if($packet->type === MCPEPlayerListPacket::TYPE_ADD){
						$entry->actorUniqueId = $mcpeEntry->entityUniqueId;
						$entry->username = $mcpeEntry->username;
						$entry->xboxUserId = "";
						$entry->skin = SkinConverter::convert($mcpeEntry->skin);
					}
					$pk->entries[] = $entry;
				}
				break;
			case MCPEProtocolInfo::REMOVE_ENTITY_PACKET:
				/** @var MCPERemoveEntityPacket $packet */
				$pk = new BedrockRemoveActorPacket();
				$pk->actorUniqueId = $packet->entityUniqueId;
				break;
			case MCPEProtocolInfo::ADD_ITEM_ENTITY_PACKET:
				/** @var MCPEAddItemEntityPacket $packet */
				$pk = new BedrockAddItemActorPacket();
				$pk->actorUniqueId = $packet->entityUniqueId;
				$pk->actorRuntimeId = $packet->entityRuntimeId;
				$pk->item = ItemInstance::legacy($packet->item);
				$pk->position = new Vector3($packet->x, $packet->y, $packet->z);
				$pk->motion = new Vector3($packet->speedX, $packet->speedY, $packet->speedZ);
				$pk->metadata = self::translateMetadata($packet->metadata); //:o
				break;
			case MCPEProtocolInfo::SET_ENTITY_MOTION_PACKET:
				/** @var MCPESetEntityMotionPacket $packet */
				$pk = new BedrockSetActorMotionPacket();
				$pk->actorRuntimeId = $packet->entityRuntimeId;
				$pk->motion = new Vector3($packet->motionX, $packet->motionY, $packet->motionZ);
				break;
			case MCPEProtocolInfo::SET_ENTITY_LINK_PACKET:
				/** @var MCPESetEntityLinkPacket $packet */
				$pk = new BedrockSetActorLinkPacket();
				$pk->link = new ActorLink($packet->fromEntityUniqueId, $packet->toEntityUniqueId, $packet->type);
				break;
			case MCPEProtocolInfo::TAKE_ITEM_ENTITY_PACKET:
				/** @var MCPETakeItemEntityPacket $packet */
				$pk = new BedrockTakeItemActorPacket();
				$pk->targetRuntimeId = $packet->target;
				$pk->actorRuntimeId = $packet->eid;
				break;
			case MCPEProtocolInfo::LEVEL_SOUND_EVENT_PACKET:
				/** @var MCPELevelSoundEventPacket $packet */
				static $soundIds = [
					MCPELevelSoundEventPacket::SOUND_ITEM_USE_ON => BedrockLevelSoundEventPacket::SOUND_ITEM_USE_ON,
					MCPELevelSoundEventPacket::SOUND_HIT => BedrockLevelSoundEventPacket::SOUND_HIT,
					MCPELevelSoundEventPacket::SOUND_STEP => BedrockLevelSoundEventPacket::SOUND_STEP,
					MCPELevelSoundEventPacket::SOUND_JUMP => BedrockLevelSoundEventPacket::SOUND_JUMP,
					MCPELevelSoundEventPacket::SOUND_BREAK => BedrockLevelSoundEventPacket::SOUND_BREAK,
					MCPELevelSoundEventPacket::SOUND_PLACE => BedrockLevelSoundEventPacket::SOUND_PLACE,
					MCPELevelSoundEventPacket::SOUND_HEAVY_STEP => BedrockLevelSoundEventPacket::SOUND_HEAVY_STEP,
					MCPELevelSoundEventPacket::SOUND_GALLOP => BedrockLevelSoundEventPacket::SOUND_GALLOP,
					MCPELevelSoundEventPacket::SOUND_FALL => BedrockLevelSoundEventPacket::SOUND_FALL,
					MCPELevelSoundEventPacket::SOUND_AMBIENT => BedrockLevelSoundEventPacket::SOUND_AMBIENT,
					MCPELevelSoundEventPacket::SOUND_AMBIENT_BABY => BedrockLevelSoundEventPacket::SOUND_AMBIENT_BABY,
					MCPELevelSoundEventPacket::SOUND_AMBIENT_IN_WATER => BedrockLevelSoundEventPacket::SOUND_AMBIENT_IN_WATER,
					MCPELevelSoundEventPacket::SOUND_BREATHE => BedrockLevelSoundEventPacket::SOUND_BREATHE,
					MCPELevelSoundEventPacket::SOUND_DEATH => BedrockLevelSoundEventPacket::SOUND_DEATH,
					MCPELevelSoundEventPacket::SOUND_DEATH_IN_WATER => BedrockLevelSoundEventPacket::SOUND_DEATH_IN_WATER,
					MCPELevelSoundEventPacket::SOUND_DEATH_TO_ZOMBIE => BedrockLevelSoundEventPacket::SOUND_DEATH_TO_ZOMBIE,
					MCPELevelSoundEventPacket::SOUND_HURT => BedrockLevelSoundEventPacket::SOUND_HURT,
					MCPELevelSoundEventPacket::SOUND_HURT_IN_WATER => BedrockLevelSoundEventPacket::SOUND_HURT_IN_WATER,
					MCPELevelSoundEventPacket::SOUND_MAD => BedrockLevelSoundEventPacket::SOUND_MAD,
					MCPELevelSoundEventPacket::SOUND_BOOST => BedrockLevelSoundEventPacket::SOUND_BOOST,
					MCPELevelSoundEventPacket::SOUND_BOW => BedrockLevelSoundEventPacket::SOUND_BOW,
					MCPELevelSoundEventPacket::SOUND_SQUISH_BIG => BedrockLevelSoundEventPacket::SOUND_SQUISH_BIG,
					MCPELevelSoundEventPacket::SOUND_SQUISH_SMALL => BedrockLevelSoundEventPacket::SOUND_SQUISH_SMALL,
					MCPELevelSoundEventPacket::SOUND_FALL_BIG => BedrockLevelSoundEventPacket::SOUND_FALL_BIG,
					MCPELevelSoundEventPacket::SOUND_FALL_SMALL => BedrockLevelSoundEventPacket::SOUND_FALL_SMALL,
					MCPELevelSoundEventPacket::SOUND_SPLASH => BedrockLevelSoundEventPacket::SOUND_SPLASH,
					MCPELevelSoundEventPacket::SOUND_FIZZ => BedrockLevelSoundEventPacket::SOUND_FIZZ,
					MCPELevelSoundEventPacket::SOUND_FLAP => BedrockLevelSoundEventPacket::SOUND_FLAP,
					MCPELevelSoundEventPacket::SOUND_SWIM => BedrockLevelSoundEventPacket::SOUND_SWIM,
					MCPELevelSoundEventPacket::SOUND_DRINK => BedrockLevelSoundEventPacket::SOUND_DRINK,
					MCPELevelSoundEventPacket::SOUND_EAT => BedrockLevelSoundEventPacket::SOUND_EAT,
					MCPELevelSoundEventPacket::SOUND_TAKEOFF => BedrockLevelSoundEventPacket::SOUND_TAKEOFF,
					MCPELevelSoundEventPacket::SOUND_SHAKE => BedrockLevelSoundEventPacket::SOUND_SHAKE,
					MCPELevelSoundEventPacket::SOUND_PLOP => BedrockLevelSoundEventPacket::SOUND_PLOP,
					MCPELevelSoundEventPacket::SOUND_LAND => BedrockLevelSoundEventPacket::SOUND_LAND,
					MCPELevelSoundEventPacket::SOUND_SADDLE => BedrockLevelSoundEventPacket::SOUND_SADDLE,
					MCPELevelSoundEventPacket::SOUND_ARMOR => BedrockLevelSoundEventPacket::SOUND_ARMOR,
					MCPELevelSoundEventPacket::SOUND_ADD_CHEST => BedrockLevelSoundEventPacket::SOUND_ADD_CHEST,
					MCPELevelSoundEventPacket::SOUND_THROW => BedrockLevelSoundEventPacket::SOUND_THROW,
					MCPELevelSoundEventPacket::SOUND_ATTACK => BedrockLevelSoundEventPacket::SOUND_ATTACK,
					MCPELevelSoundEventPacket::SOUND_ATTACK_NODAMAGE => BedrockLevelSoundEventPacket::SOUND_ATTACK_NODAMAGE,
					MCPELevelSoundEventPacket::SOUND_WARN => BedrockLevelSoundEventPacket::SOUND_WARN,
					MCPELevelSoundEventPacket::SOUND_SHEAR => BedrockLevelSoundEventPacket::SOUND_SHEAR,
					MCPELevelSoundEventPacket::SOUND_MILK => BedrockLevelSoundEventPacket::SOUND_MILK,
					MCPELevelSoundEventPacket::SOUND_THUNDER => BedrockLevelSoundEventPacket::SOUND_THUNDER,
					MCPELevelSoundEventPacket::SOUND_EXPLODE => BedrockLevelSoundEventPacket::SOUND_EXPLODE,
					MCPELevelSoundEventPacket::SOUND_FIRE => BedrockLevelSoundEventPacket::SOUND_FIRE,
					MCPELevelSoundEventPacket::SOUND_IGNITE => BedrockLevelSoundEventPacket::SOUND_IGNITE,
					MCPELevelSoundEventPacket::SOUND_FUSE => BedrockLevelSoundEventPacket::SOUND_FUSE,
					MCPELevelSoundEventPacket::SOUND_STARE => BedrockLevelSoundEventPacket::SOUND_STARE,
					MCPELevelSoundEventPacket::SOUND_SPAWN => BedrockLevelSoundEventPacket::SOUND_SPAWN,
					MCPELevelSoundEventPacket::SOUND_SHOOT => BedrockLevelSoundEventPacket::SOUND_SHOOT,
					MCPELevelSoundEventPacket::SOUND_BREAK_BLOCK => BedrockLevelSoundEventPacket::SOUND_BREAK_BLOCK,
					MCPELevelSoundEventPacket::SOUND_REMEDY => BedrockLevelSoundEventPacket::SOUND_REMEDY,
					MCPELevelSoundEventPacket::SOUND_UNFECT => BedrockLevelSoundEventPacket::SOUND_UNFECT,
					MCPELevelSoundEventPacket::SOUND_LEVELUP => BedrockLevelSoundEventPacket::SOUND_LEVELUP,
					MCPELevelSoundEventPacket::SOUND_BOW_HIT => BedrockLevelSoundEventPacket::SOUND_BOW_HIT,
					MCPELevelSoundEventPacket::SOUND_BULLET_HIT => BedrockLevelSoundEventPacket::SOUND_BULLET_HIT,
					MCPELevelSoundEventPacket::SOUND_EXTINGUISH_FIRE => BedrockLevelSoundEventPacket::SOUND_EXTINGUISH_FIRE,
					MCPELevelSoundEventPacket::SOUND_ITEM_FIZZ => BedrockLevelSoundEventPacket::SOUND_ITEM_FIZZ,
					MCPELevelSoundEventPacket::SOUND_CHEST_OPEN => BedrockLevelSoundEventPacket::SOUND_CHEST_OPEN,
					MCPELevelSoundEventPacket::SOUND_CHEST_CLOSED => BedrockLevelSoundEventPacket::SOUND_CHEST_CLOSED,
					MCPELevelSoundEventPacket::SOUND_SHULKERBOX_OPEN => BedrockLevelSoundEventPacket::SOUND_SHULKERBOX_OPEN,
					MCPELevelSoundEventPacket::SOUND_SHULKERBOX_CLOSED => BedrockLevelSoundEventPacket::SOUND_SHULKERBOX_CLOSED,
					MCPELevelSoundEventPacket::SOUND_POWER_ON => BedrockLevelSoundEventPacket::SOUND_POWER_ON,
					MCPELevelSoundEventPacket::SOUND_POWER_OFF => BedrockLevelSoundEventPacket::SOUND_POWER_OFF,
					MCPELevelSoundEventPacket::SOUND_ATTACH => BedrockLevelSoundEventPacket::SOUND_ATTACH,
					MCPELevelSoundEventPacket::SOUND_DETACH => BedrockLevelSoundEventPacket::SOUND_DETACH,
					MCPELevelSoundEventPacket::SOUND_DENY => BedrockLevelSoundEventPacket::SOUND_DENY,
					MCPELevelSoundEventPacket::SOUND_TRIPOD => BedrockLevelSoundEventPacket::SOUND_TRIPOD,
					MCPELevelSoundEventPacket::SOUND_POP => BedrockLevelSoundEventPacket::SOUND_POP,
					MCPELevelSoundEventPacket::SOUND_DROP_SLOT => BedrockLevelSoundEventPacket::SOUND_DROP_SLOT,
					MCPELevelSoundEventPacket::SOUND_NOTE => BedrockLevelSoundEventPacket::SOUND_NOTE,
					MCPELevelSoundEventPacket::SOUND_THORNS => BedrockLevelSoundEventPacket::SOUND_THORNS,
					MCPELevelSoundEventPacket::SOUND_PISTON_IN => BedrockLevelSoundEventPacket::SOUND_PISTON_IN,
					MCPELevelSoundEventPacket::SOUND_PISTON_OUT => BedrockLevelSoundEventPacket::SOUND_PISTON_OUT,
					MCPELevelSoundEventPacket::SOUND_PORTAL => BedrockLevelSoundEventPacket::SOUND_PORTAL,
					MCPELevelSoundEventPacket::SOUND_WATER => BedrockLevelSoundEventPacket::SOUND_WATER,
					MCPELevelSoundEventPacket::SOUND_LAVA_POP => BedrockLevelSoundEventPacket::SOUND_LAVA_POP,
					MCPELevelSoundEventPacket::SOUND_LAVA => BedrockLevelSoundEventPacket::SOUND_LAVA,
					MCPELevelSoundEventPacket::SOUND_BURP => BedrockLevelSoundEventPacket::SOUND_BURP,
					MCPELevelSoundEventPacket::SOUND_BUCKET_FILL_WATER => BedrockLevelSoundEventPacket::SOUND_BUCKET_FILL_WATER,
					MCPELevelSoundEventPacket::SOUND_BUCKET_FILL_LAVA => BedrockLevelSoundEventPacket::SOUND_BUCKET_FILL_LAVA,
					MCPELevelSoundEventPacket::SOUND_BUCKET_EMPTY_WATER => BedrockLevelSoundEventPacket::SOUND_BUCKET_EMPTY_WATER,
					MCPELevelSoundEventPacket::SOUND_BUCKET_EMPTY_LAVA => BedrockLevelSoundEventPacket::SOUND_BUCKET_EMPTY_LAVA,
					MCPELevelSoundEventPacket::SOUND_ELDERGUARDIAN_CURSE => BedrockLevelSoundEventPacket::SOUND_ELDERGUARDIAN_CURSE,
					MCPELevelSoundEventPacket::SOUND_MOB_WARNING => BedrockLevelSoundEventPacket::SOUND_MOB_WARNING,
					MCPELevelSoundEventPacket::SOUND_MOB_WARNING_BABY => BedrockLevelSoundEventPacket::SOUND_MOB_WARNING_BABY,
					MCPELevelSoundEventPacket::SOUND_TELEPORT => BedrockLevelSoundEventPacket::SOUND_TELEPORT,
					MCPELevelSoundEventPacket::SOUND_SHULKER_OPEN => BedrockLevelSoundEventPacket::SOUND_SHULKER_OPEN,
					MCPELevelSoundEventPacket::SOUND_SHULKER_CLOSE => BedrockLevelSoundEventPacket::SOUND_SHULKER_CLOSE,
					MCPELevelSoundEventPacket::SOUND_HAGGLE => BedrockLevelSoundEventPacket::SOUND_HAGGLE,
					MCPELevelSoundEventPacket::SOUND_HAGGLE_YES => BedrockLevelSoundEventPacket::SOUND_HAGGLE_YES,
					MCPELevelSoundEventPacket::SOUND_HAGGLE_NO => BedrockLevelSoundEventPacket::SOUND_HAGGLE_NO,
					MCPELevelSoundEventPacket::SOUND_HAGGLE_IDLE => BedrockLevelSoundEventPacket::SOUND_HAGGLE_IDLE,
					MCPELevelSoundEventPacket::SOUND_CHORUSGROW => BedrockLevelSoundEventPacket::SOUND_CHORUSGROW,
					MCPELevelSoundEventPacket::SOUND_CHORUSDEATH => BedrockLevelSoundEventPacket::SOUND_CHORUSDEATH,
					MCPELevelSoundEventPacket::SOUND_GLASS => BedrockLevelSoundEventPacket::SOUND_GLASS,
					MCPELevelSoundEventPacket::SOUND_CAST_SPELL => BedrockLevelSoundEventPacket::SOUND_CAST_SPELL,
					MCPELevelSoundEventPacket::SOUND_PREPARE_ATTACK => BedrockLevelSoundEventPacket::SOUND_PREPARE_ATTACK,
					MCPELevelSoundEventPacket::SOUND_PREPARE_SUMMON => BedrockLevelSoundEventPacket::SOUND_PREPARE_SUMMON,
					MCPELevelSoundEventPacket::SOUND_PREPARE_WOLOLO => BedrockLevelSoundEventPacket::SOUND_PREPARE_WOLOLO,
					MCPELevelSoundEventPacket::SOUND_FANG => BedrockLevelSoundEventPacket::SOUND_FANG,
					MCPELevelSoundEventPacket::SOUND_CHARGE => BedrockLevelSoundEventPacket::SOUND_CHARGE,
					MCPELevelSoundEventPacket::SOUND_CAMERA_TAKE_PICTURE => BedrockLevelSoundEventPacket::SOUND_CAMERA_TAKE_PICTURE,
					MCPELevelSoundEventPacket::SOUND_DEFAULT => BedrockLevelSoundEventPacket::SOUND_DEFAULT,
					MCPELevelSoundEventPacket::SOUND_UNDEFINED => BedrockLevelSoundEventPacket::SOUND_UNDEFINED,
				];
				if(isset($soundIds[$packet->sound])){
					$pk = new BedrockLevelSoundEventPacket();
					$pk->sound = $soundIds[$packet->sound];
					$pk->position = new Vector3($packet->x, $packet->y, $packet->z);
					$pk->extraData = $packet->extraData;
					if($packet->sound === MCPELevelSoundEventPacket::SOUND_HIT){
						$pk->extraData = BlockPalette::getRuntimeFromLegacyId($packet->extraData);
					}elseif($packet->sound === MCPELevelSoundEventPacket::SOUND_PLACE){
						$pk->extraData = BlockPalette::getRuntimeFromLegacyId($packet->extraData);
						$pk->actorType = "";
					}elseif($packet->entityType === 0x13f){
						$pk->actorType = "minecraft:player";
					}else{
						$pk->actorType = ActorMapping::getStringIdFromLegacyId($packet->entityType);
					}
					$pk->isBabyMob = $packet->isBabyMob;
					$pk->disableRelativeVolume = $packet->disableRelativeVolume;
					break;
				}
				return null;
			case MCPEProtocolInfo::ENTITY_EVENT_PACKET:
				/** @var MCPEEntityEventPacket $packet */
				$pk = new BedrockActorEventPacket();
				$pk->actorRuntimeId = $packet->entityRuntimeId;
				$pk->event = $packet->event;
				if($packet->event === EntityEventPacket::EATING_ITEM){
					$pk->data = $packet->data << 16;
				}else{
					$pk->data = $packet->data;
				}
				break;
			case MCPEProtocolInfo::LEVEL_EVENT_PACKET:
				/** @var MCPELevelEventPacket $packet */
				if(($packet->evid & MCPELevelEventPacket::EVENT_ADD_PARTICLE_MASK) > 0){ //Particle
					static $legacyMaskIds = [
						MCPELevelEventParticleIds::MOB_SPELL => LevelEventParticleIds::MOB_SPELL,
						MCPELevelEventParticleIds::MOB_SPELL_INSTANTANEOUS => LevelEventParticleIds::MOB_SPELL_INSTANTANEOUS,
						MCPELevelEventParticleIds::ITEM_BREAK => LevelEventParticleIds::ITEM_BREAK,
					];
					static $particleIds = [
						MCPELevelEventParticleIds::SMOKE => LevelEventParticleIds::SMOKE,
						MCPELevelEventParticleIds::FLAME => LevelEventParticleIds::FLAME,
						MCPELevelEventParticleIds::LARGE_SMOKE => LevelEventParticleIds::LARGE_SMOKE,
						MCPELevelEventParticleIds::RISING_RED_DUST => LevelEventParticleIds::RISING_RED_DUST,
						MCPELevelEventParticleIds::SNOWBALL_POOF => LevelEventParticleIds::SNOWBALL_POOF,
						MCPELevelEventParticleIds::HEART => LevelEventParticleIds::HEART,
						MCPELevelEventParticleIds::TERRAIN => LevelEventParticleIds::TERRAIN,
						MCPELevelEventParticleIds::PORTAL => LevelEventParticleIds::PORTAL,
						MCPELevelEventParticleIds::WATER_WAKE => LevelEventParticleIds::WATER_WAKE,
						MCPELevelEventParticleIds::DRIP_WATER => LevelEventParticleIds::DRIP_WATER,
						MCPELevelEventParticleIds::DRIP_LAVA => LevelEventParticleIds::DRIP_LAVA,
						MCPELevelEventParticleIds::SLIME => LevelEventParticleIds::SLIME,
						MCPELevelEventParticleIds::RAIN_SPLASH => LevelEventParticleIds::RAIN_SPLASH,
						MCPELevelEventParticleIds::VILLAGER_ANGRY => LevelEventParticleIds::VILLAGER_ANGRY,
						MCPELevelEventParticleIds::VILLAGER_HAPPY => LevelEventParticleIds::VILLAGER_HAPPY,
						MCPELevelEventParticleIds::ENCHANTMENT_TABLE => LevelEventParticleIds::ENCHANTMENT_TABLE,
						MCPELevelEventParticleIds::TRACKING_EMITTER => LevelEventParticleIds::TRACKING_EMITTER,
						MCPELevelEventParticleIds::WITCH_SPELL => LevelEventParticleIds::WITCH_SPELL,
						MCPELevelEventParticleIds::END_ROD => LevelEventParticleIds::END_ROD,
					];
					static $stringMap = [
						MCPELevelEventParticleIds::BUBBLE => ParticleEffectIds::BASIC_BUBBLE,
						MCPELevelEventParticleIds::CRITICAL => ParticleEffectIds::BASIC_CRIT,
						MCPELevelEventParticleIds::EXPLODE => ParticleEffectIds::EXPLOSION_LEVEL,
						MCPELevelEventParticleIds::EVAPORATION => ParticleEffectIds::WATER_EVAPORATION_BUCKET,
						MCPELevelEventParticleIds::LAVA => ParticleEffectIds::LAVA_PARTICLE,
						MCPELevelEventParticleIds::REDSTONE => ParticleEffectIds::REDSTONE_WIRE_DUST,
						MCPELevelEventParticleIds::HUGE_EXPLODE => ParticleEffectIds::HUGE_EXPLOSION_LEVEL,
						MCPELevelEventParticleIds::HUGE_EXPLODE_SEED => ParticleEffectIds::HUGE_EXPLOSION_LAB_MISC,
						MCPELevelEventParticleIds::MOB_FLAME => ParticleEffectIds::MOBFLAME,
						MCPELevelEventParticleIds::WATER_SPLASH => ParticleEffectIds::WATER_SPLASH,
						MCPELevelEventParticleIds::FALLING_DUST => ParticleEffectIds::FALLING_DUST_SCAFFOLDING,
						MCPELevelEventParticleIds::INK => ParticleEffectIds::INK,
						MCPELevelEventParticleIds::NOTE => ParticleEffectIds::NOTE
					];

					$particleId = $packet->evid & 0x3fff;
					if(isset($legacyMaskIds[$particleId])){
						$packet->evid = BedrockLevelEventPacket::EVENT_ADD_PARTICLE_LEGACY_MASK | $legacyMaskIds[$particleId];
					}elseif(isset($particleIds[$particleId])){
						$packet->evid = BedrockLevelEventPacket::EVENT_PARTICLE_GENERIC_SPAWN;
						$packet->data = $particleIds[$particleId];
					}elseif(isset($stringMap[$particleId])){
						$pk = new SpawnParticleEffectPacket();
						$pk->position = new Vector3($packet->x, $packet->y, $packet->z);
						$pk->particleName = $stringMap[$particleId];
						break;
					}else{
						return null;
					}
				}
				$pk = new BedrockLevelEventPacket();
				$pk->evid = $packet->evid;
				$pk->position = new Vector3($packet->x, $packet->y, $packet->z);
				switch($pk->evid){
					case BedrockLevelEventPacket::EVENT_PARTICLE_PUNCH_BLOCK:
						$id = $packet->data & 0xff;
						$meta = ($packet->data >> 8) & 0xff;
						$face = $packet->data >> 16;
						$pk->data = BlockPalette::getRuntimeFromLegacyId($id, $meta) | ($face << 24);
						break;
					case BedrockLevelEventPacket::EVENT_PARTICLE_DESTROY:
						$pk->data = BlockPalette::getRuntimeFromLegacyId($packet->data & 0xff, $packet->data >> 8);
						break;
					default:
						$pk->data = $packet->data;
				}
				break;
			case MCPEProtocolInfo::ANIMATE_PACKET:
				/** @var MCPEAnimatePacket $packet */
				$pk = new BedrockAnimatePacket();
				$pk->action = $packet->action;
				$pk->actorRuntimeId = $packet->entityRuntimeId;
				$pk->float = $packet->float;
				break;
			case MCPEProtocolInfo::UPDATE_BLOCK_PACKET:
				/** @var MCPEUpdateBlockPacket $packet */
				$pk = new BedrockUpdateBlockPacket();
				$pk->x = $packet->x;
				$pk->y = $packet->y;
				$pk->z = $packet->z;
				$pk->blockRuntimeId = BlockPalette::getRuntimeFromLegacyId($packet->blockId, $packet->blockData);
				break;
			case MCPEProtocolInfo::CRAFTING_DATA_PACKET:
				/** @var MCPECraftingDataPacket $packet */
				$pk = new BedrockCraftingDataPacket();
				$pk->entries = $packet->entries;
				$pk->cleanRecipes = false; //$packet->cleanRecipes;
				break;
			case MCPEProtocolInfo::BLOCK_EVENT_PACKET:
				/** @var MCPEBlockEventPacket $packet */
				$pk = new BedrockBlockEventPacket();
				$pk->x = $packet->x;
				$pk->y = $packet->y;
				$pk->z = $packet->z;
				$pk->eventType = $packet->eventType;
				$pk->eventData = $packet->eventData;
				if($pk->eventType === MCPEBlockEventPacket::TYPE_CHEST and $pk->eventData === MCPEBlockEventPacket::DATA_CHEST_OPEN){
					$pk->eventData = BedrockBlockEventPacket::DATA_CHEST_OPEN;
				}
				break;
			case MCPEProtocolInfo::CHANGE_DIMENSION_PACKET:
				/** @var MCPEChangeDimensionPacket $packet */
				$pk = new BedrockChangeDimensionPacket();
				$pk->dimension = $packet->dimension;
				$pk->position = new Vector3($packet->x, $packet->y, $packet->z);
				$pk->respawn = $packet->respawn;
				break;
			case MCPEProtocolInfo::CLIENTBOUND_MAP_ITEM_DATA_PACKET:
				/** @var MCPEClientboundMapItemDataPacket $packet */
				$pk = new BedrockClientboundMapItemDataPacket();
				$pk->mapId = $packet->mapId;
				$pk->type = $packet->type;
				$pk->actorUniqueIds = $packet->eids;
				$pk->scale = $packet->scale;
				$pk->decorations = $packet->decorations;
				$pk->width = $packet->width;
				$pk->height = $packet->height;
				$pk->xOffset = $packet->xOffset;
				$pk->yOffset = $packet->yOffset;
				$pk->colors = $packet->colors;
				break;
			case MCPEProtocolInfo::MOB_ARMOR_EQUIPMENT_PACKET:
				/** @var MCPEMobArmorEquipmentPacket $packet */
				$pk = new BedrockMobArmorEquipmentPacket();
				$pk->actorRuntimeId = $packet->entityRuntimeId;
				$pk->slots = array_map([ItemInstance::class, 'legacy'], $packet->slots);
				break;
			case MCPEProtocolInfo::MOB_EFFECT_PACKET:
				/** @var MCPEMobEffectPacket $packet */
				$pk = new BedrockMobEffectPacket();
				$pk->actorRuntimeId = $packet->entityRuntimeId;
				$pk->eventId = $packet->eventId;
				$pk->effectId = $packet->effectId;
				$pk->amplifier = $packet->amplifier;
				$pk->particles = $packet->particles;
				$pk->duration = $packet->duration;
				break;
			case MCPEProtocolInfo::MOB_EQUIPMENT_PACKET:
				/** @var MCPEMobEquipmentPacket $packet */
				$pk = new BedrockMobEquipmentPacket();
				$pk->actorRuntimeId = $packet->entityRuntimeId;
				$pk->item = ItemInstance::legacy($packet->item);
				$pk->inventorySlot = $packet->inventorySlot;
				$pk->hotbarSlot = $packet->hotbarSlot;
				$pk->windowId = $packet->windowId;
				break;
			case MCPEProtocolInfo::PLAY_SOUND_PACKET:
				/** @var MCPEPlaySoundPacket $packet */
				$pk = new BedrockPlaySoundPacket();
				$pk->soundName = $packet->soundName;
				$pk->x = $packet->x;
				$pk->y = $packet->y;
				$pk->z = $packet->z;
				$pk->volume = $packet->volume;
				$pk->pitch = $packet->pitch;
				break;
			case MCPEProtocolInfo::RESPAWN_PACKET:
				/** @var MCPERespawnPacket $packet */
				$pk = new BedrockRespawnPacket();
				$pk->position = new Vector3($packet->x, $packet->y, $packet->z);
				break;
			case MCPEProtocolInfo::SET_DIFFICULTY_PACKET:
				/** @var MCPESetDifficultyPacket $packet */
				$pk = new BedrockSetDifficultyPacket();
				$pk->difficulty = $packet->difficulty;
				break;
			case MCPEProtocolInfo::SET_ENTITY_DATA_PACKET:
				/** @var MCPESetEntityDataPacket $packet */
				$pk = new BedrockSetActorDataPacket();
				$pk->actorRuntimeId = $packet->entityRuntimeId;
				$pk->metadata = self::translateMetadata($packet->metadata); //:o
				break;
			case MCPEProtocolInfo::SET_SPAWN_POSITION_PACKET:
				/** @var MCPESetSpawnPositionPacket $packet */
				$pk = new BedrockSetSpawnPositionPacket();
				$pk->spawnType = $packet->spawnType;
				[$pk->x, $pk->y, $pk->z] = [$packet->x, $packet->y, $packet->z];
				[$pk->spawnX, $pk->spawnY, $pk->spawnZ] = [$packet->x, $packet->y, $packet->z];
				break;
			case MCPEProtocolInfo::STOP_SOUND_PACKET:
				/** @var MCPEStopSoundPacket $packet */
				$pk = new BedrockStopSoundPacket();
				$pk->soundName = $packet->soundName;
				$pk->stopAll = $packet->stopAll;
				break;
			case MCPEProtocolInfo::BOSS_EVENT_PACKET:
				/** @var MCPEBossEventPacket $packet */
				$pk = new BedrockBossEventPacket();
				$pk->bossUniqueId = $packet->bossEid;
				$pk->eventType = $packet->eventType;
				$pk->playerUniqueId = $packet->playerEid;
				$pk->healthPercent = $packet->healthPercent;
				$pk->title = $packet->title;
				$pk->unknownShort = $packet->unknownShort;
				$pk->color = $packet->color;
				$pk->overlay = $packet->overlay;
				break;
			default:
				return null;
		}
		return $pk;
	}

	/**
	 * Translates entity metadata to Bedrock Edition client.
	 *
	 * @param array $metadata
	 *
	 * @return array
	 */
	public static function translateMetadata(array $metadata) : array{
		static $properties = [
			Entity::DATA_FLAGS => ActorMetadataProperties::FLAGS,
			Entity::DATA_HEALTH => ActorMetadataProperties::HEALTH,
			Entity::DATA_VARIANT => ActorMetadataProperties::VARIANT,
			Entity::DATA_COLOR => ActorMetadataProperties::COLOR,
			Entity::DATA_NAMETAG => ActorMetadataProperties::NAMETAG,
			Entity::DATA_OWNER_EID => ActorMetadataProperties::OWNER_EID,
			Entity::DATA_TARGET_EID => ActorMetadataProperties::TARGET_EID,
			Entity::DATA_AIR => ActorMetadataProperties::AIR,
			Entity::DATA_POTION_COLOR => ActorMetadataProperties::POTION_COLOR,
			Entity::DATA_POTION_AMBIENT => ActorMetadataProperties::POTION_AMBIENT,
			Entity::DATA_HURT_TIME => ActorMetadataProperties::HURT_TIME,
			Entity::DATA_HURT_DIRECTION => ActorMetadataProperties::HURT_DIRECTION,
			Entity::DATA_PADDLE_TIME_LEFT => ActorMetadataProperties::PADDLE_TIME_LEFT,
			Entity::DATA_PADDLE_TIME_RIGHT => ActorMetadataProperties::PADDLE_TIME_RIGHT,
			Entity::DATA_EXPERIENCE_VALUE => ActorMetadataProperties::EXPERIENCE_VALUE,
			Entity::DATA_MINECART_DISPLAY_BLOCK => ActorMetadataProperties::MINECART_DISPLAY_BLOCK,
			Entity::DATA_MINECART_DISPLAY_OFFSET => ActorMetadataProperties::MINECART_DISPLAY_OFFSET,
			Entity::DATA_MINECART_HAS_DISPLAY => ActorMetadataProperties::MINECART_HAS_DISPLAY,
			Entity::DATA_ENDERMAN_HELD_ITEM_ID => ActorMetadataProperties::ENDERMAN_HELD_ITEM_ID,
			Entity::DATA_ENTITY_AGE => ActorMetadataProperties::ENTITY_AGE,
			Human::DATA_PLAYER_FLAGS => ActorMetadataProperties::PLAYER_FLAGS,
			Human::DATA_PLAYER_INDEX => ActorMetadataProperties::PLAYER_INDEX,
			Human::DATA_PLAYER_BED_POSITION => ActorMetadataProperties::PLAYER_BED_POSITION,
			Entity::DATA_FIREBALL_POWER_X => ActorMetadataProperties::FIREBALL_POWER_X,
			Entity::DATA_FIREBALL_POWER_Y => ActorMetadataProperties::FIREBALL_POWER_Y,
			Entity::DATA_FIREBALL_POWER_Z => ActorMetadataProperties::FIREBALL_POWER_Z,
			Entity::DATA_POTION_AUX_VALUE => ActorMetadataProperties::POTION_AUX_VALUE,
			Entity::DATA_LEAD_HOLDER_EID => ActorMetadataProperties::LEAD_HOLDER_EID,
			Entity::DATA_SCALE => ActorMetadataProperties::SCALE,
			Entity::DATA_INTERACTIVE_TAG => ActorMetadataProperties::INTERACTIVE_TAG,
			Entity::DATA_NPC_SKIN_ID => ActorMetadataProperties::NPC_SKIN_INDEX,
			Entity::DATA_MAX_AIR => ActorMetadataProperties::MAX_AIR,
			Entity::DATA_MARK_VARIANT => ActorMetadataProperties::MARK_VARIANT,
			Entity::DATA_CONTAINER_TYPE => ActorMetadataProperties::CONTAINER_TYPE,
			Entity::DATA_CONTAINER_BASE_SIZE => ActorMetadataProperties::CONTAINER_BASE_SIZE,
			Entity::DATA_CONTAINER_EXTRA_SLOTS_PER_STRENGTH => ActorMetadataProperties::CONTAINER_EXTRA_SLOTS_PER_STRENGTH,
			Entity::DATA_BLOCK_TARGET => ActorMetadataProperties::BLOCK_TARGET,
			Entity::DATA_WITHER_INVULNERABLE_TICKS => ActorMetadataProperties::WITHER_INVULNERABLE_TICKS,
			Entity::DATA_WITHER_TARGET_1 => ActorMetadataProperties::WITHER_TARGET_1,
			Entity::DATA_WITHER_TARGET_2 => ActorMetadataProperties::WITHER_TARGET_2,
			Entity::DATA_WITHER_TARGET_3 => ActorMetadataProperties::WITHER_TARGET_3,
			Entity::DATA_BOUNDING_BOX_WIDTH => ActorMetadataProperties::BOUNDING_BOX_WIDTH,
			Entity::DATA_BOUNDING_BOX_HEIGHT => ActorMetadataProperties::BOUNDING_BOX_HEIGHT,
			Entity::DATA_FUSE_LENGTH => ActorMetadataProperties::FUSE_LENGTH,
			Entity::DATA_RIDER_SEAT_POSITION => ActorMetadataProperties::RIDER_SEAT_POSITION,
			Entity::DATA_RIDER_ROTATION_LOCKED => ActorMetadataProperties::RIDER_ROTATION_LOCKED,
			Entity::DATA_RIDER_MAX_ROTATION => ActorMetadataProperties::RIDER_MAX_ROTATION,
			Entity::DATA_RIDER_MIN_ROTATION => ActorMetadataProperties::RIDER_MIN_ROTATION,
			Entity::DATA_AREA_EFFECT_CLOUD_RADIUS => ActorMetadataProperties::AREA_EFFECT_CLOUD_RADIUS,
			Entity::DATA_AREA_EFFECT_CLOUD_WAITING => ActorMetadataProperties::AREA_EFFECT_CLOUD_WAITING,
			Entity::DATA_AREA_EFFECT_CLOUD_PARTICLE_ID => ActorMetadataProperties::AREA_EFFECT_CLOUD_PARTICLE_ID,
			Entity::DATA_SHULKER_ATTACH_FACE => ActorMetadataProperties::SHULKER_ATTACH_FACE,
			Entity::DATA_SHULKER_ATTACH_POS => ActorMetadataProperties::SHULKER_ATTACH_POS,
			Entity::DATA_TRADING_PLAYER_EID => ActorMetadataProperties::TRADING_PLAYER_EID,
			Entity::DATA_COMMAND_BLOCK_COMMAND => ActorMetadataProperties::COMMAND_BLOCK_COMMAND,
			Entity::DATA_COMMAND_BLOCK_LAST_OUTPUT => ActorMetadataProperties::COMMAND_BLOCK_LAST_OUTPUT,
			Entity::DATA_COMMAND_BLOCK_TRACK_OUTPUT => ActorMetadataProperties::COMMAND_BLOCK_TRACK_OUTPUT,
			Entity::DATA_CONTROLLING_RIDER_SEAT_NUMBER => ActorMetadataProperties::CONTROLLING_RIDER_SEAT_NUMBER,
			Entity::DATA_STRENGTH => ActorMetadataProperties::STRENGTH,
			Entity::DATA_MAX_STRENGTH => ActorMetadataProperties::MAX_STRENGTH,
		];
		$result = [];
		foreach($metadata as $k => $v){
			if(isset($properties[$k])){
				if($v[0] === Entity::DATA_TYPE_SLOT){
					$v = [ActorMetadataTypes::NBT, $v[1]->getNamedTag()];
				}

				$result[$properties[$k]] = $v;
			}
		}
		if(isset($result[ActorMetadataProperties::FLAGS])){
			$old_flags = $result[ActorMetadataProperties::FLAGS][1];
			$flags = 0;
			static $flagsId = [
				Entity::DATA_FLAG_ONFIRE => ActorMetadataFlags::ONFIRE,
				Entity::DATA_FLAG_SNEAKING => ActorMetadataFlags::SNEAKING,
				Entity::DATA_FLAG_RIDING => ActorMetadataFlags::RIDING,
				Entity::DATA_FLAG_SPRINTING => ActorMetadataFlags::SPRINTING,
				Entity::DATA_FLAG_ACTION => ActorMetadataFlags::ACTION,
				Entity::DATA_FLAG_INVISIBLE => ActorMetadataFlags::INVISIBLE,
				Entity::DATA_FLAG_TEMPTED => ActorMetadataFlags::TEMPTED,
				Entity::DATA_FLAG_INLOVE => ActorMetadataFlags::INLOVE,
				Entity::DATA_FLAG_SADDLED => ActorMetadataFlags::SADDLED,
				Entity::DATA_FLAG_POWERED => ActorMetadataFlags::POWERED,
				Entity::DATA_FLAG_IGNITED => ActorMetadataFlags::IGNITED,
				Entity::DATA_FLAG_BABY => ActorMetadataFlags::BABY,
				Entity::DATA_FLAG_CONVERTING => ActorMetadataFlags::CONVERTING,
				Entity::DATA_FLAG_CRITICAL => ActorMetadataFlags::CRITICAL,
				Entity::DATA_FLAG_CAN_SHOW_NAMETAG => ActorMetadataFlags::CAN_SHOW_NAMETAG,
				Entity::DATA_FLAG_ALWAYS_SHOW_NAMETAG => ActorMetadataFlags::ALWAYS_SHOW_NAMETAG,
				Entity::DATA_FLAG_IMMOBILE => ActorMetadataFlags::IMMOBILE,
				Entity::DATA_FLAG_SILENT => ActorMetadataFlags::SILENT,
				Entity::DATA_FLAG_WALLCLIMBING => ActorMetadataFlags::WALLCLIMBING,
				Entity::DATA_FLAG_CAN_CLIMB => ActorMetadataFlags::CAN_CLIMB,
				Entity::DATA_FLAG_SWIMMER => ActorMetadataFlags::SWIMMER,
				Entity::DATA_FLAG_CAN_FLY => ActorMetadataFlags::CAN_FLY,
				Entity::DATA_FLAG_RESTING => ActorMetadataFlags::RESTING,
				Entity::DATA_FLAG_SITTING => ActorMetadataFlags::SITTING,
				Entity::DATA_FLAG_ANGRY => ActorMetadataFlags::ANGRY,
				Entity::DATA_FLAG_INTERESTED => ActorMetadataFlags::INTERESTED,
				Entity::DATA_FLAG_CHARGED => ActorMetadataFlags::CHARGED,
				Entity::DATA_FLAG_TAMED => ActorMetadataFlags::TAMED,
				Entity::DATA_FLAG_LEASHED => ActorMetadataFlags::LEASHED,
				Entity::DATA_FLAG_SHEARED => ActorMetadataFlags::SHEARED,
				Entity::DATA_FLAG_GLIDING => ActorMetadataFlags::GLIDING,
				Entity::DATA_FLAG_ELDER => ActorMetadataFlags::ELDER,
				Entity::DATA_FLAG_MOVING => ActorMetadataFlags::MOVING,
				Entity::DATA_FLAG_BREATHING => ActorMetadataFlags::BREATHING,
				Entity::DATA_FLAG_CHESTED => ActorMetadataFlags::CHESTED,
				Entity::DATA_FLAG_STACKABLE => ActorMetadataFlags::STACKABLE,
				Entity::DATA_FLAG_SHOWBASE => ActorMetadataFlags::SHOWBASE,
				Entity::DATA_FLAG_REARING => ActorMetadataFlags::REARING,
				Entity::DATA_FLAG_VIBRATING => ActorMetadataFlags::VIBRATING,
				Entity::DATA_FLAG_IDLING => ActorMetadataFlags::IDLING,
				Entity::DATA_FLAG_EVOKER_SPELL => ActorMetadataFlags::EVOKER_SPELL,
				Entity::DATA_FLAG_CHARGE_ATTACK => ActorMetadataFlags::CHARGE_ATTACK,
				Entity::DATA_FLAG_LINGER => ActorMetadataFlags::LINGER,
			];
			foreach($flagsId as $old => $new){
				if($old === Entity::DATA_FLAG_ALWAYS_SHOW_NAMETAG){
					$result[ActorMetadataProperties::ALWAYS_SHOW_NAMETAG] = [ActorMetadataTypes::BYTE, ($old_flags & (1 << $old)) > 0 ? 1 : 0];
				}elseif(($old_flags & (1 << $old)) > 0){
					$flags ^= 1 << $new;
				}
			}
			$flags ^= 1 << ActorMetadataFlags::AFFECTED_BY_GRAVITY;
			$flags ^= 1 << ActorMetadataFlags::HAS_COLLISION;
			$result[ActorMetadataProperties::FLAGS][1] = $flags;
		}
		return $result;
	}
}
