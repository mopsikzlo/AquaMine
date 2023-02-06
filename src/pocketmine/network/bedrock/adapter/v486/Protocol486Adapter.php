<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\adapter\v486;

use Closure;
use pocketmine\network\bedrock\adapter\v486\palette\BlockPalette as BlockPalette486;
use pocketmine\network\bedrock\adapter\v486\palette\ItemPalette as ItemPalette486;
use pocketmine\network\bedrock\adapter\v486\protocol\AddItemActorPacket as AddItemActorPacket486;
use pocketmine\network\bedrock\adapter\v486\protocol\AddPlayerPacket as AddPlayerPacket486;
use pocketmine\network\bedrock\adapter\v486\protocol\AvailableCommandsPacket as AvailableCommandsPacket486;
use pocketmine\network\bedrock\adapter\v486\protocol\CraftingDataPacket as CraftingDataPacket486;
use pocketmine\network\bedrock\adapter\v486\protocol\CraftingEventPacket as CraftingEventPacket486;
use pocketmine\network\bedrock\adapter\v486\protocol\CreativeContentPacket as CreativeContentPacket486;
use pocketmine\network\bedrock\adapter\v486\protocol\InventoryContentPacket as InventoryContentPacket486;
use pocketmine\network\bedrock\adapter\v486\protocol\InventorySlotPacket as InventorySlotPacket486;
use pocketmine\network\bedrock\adapter\v486\protocol\InventoryTransactionPacket as InventoryTransactionPacket486;
use pocketmine\network\bedrock\adapter\v486\protocol\LoginPacket as LoginPacket486;
use pocketmine\network\bedrock\adapter\v486\protocol\MobArmorEquipmentPacket as MobArmorEquipmentPacket486;
use pocketmine\network\bedrock\adapter\v486\protocol\MobEquipmentPacket as MobEquipmentPacket486;
use pocketmine\network\bedrock\adapter\v486\protocol\ProtocolInfo as ProtocolInfo486;
use pocketmine\network\bedrock\adapter\v486\protocol\SpawnParticleEffectPacket as SpawnParticleEffectPacket486;
use pocketmine\network\bedrock\adapter\v486\protocol\StartGamePacket as StartGamePacket486;
use pocketmine\network\bedrock\adapter\v503\Protocol503Adapter;
use pocketmine\network\bedrock\palette\BlockPalette;
use pocketmine\network\bedrock\protocol\AvailableCommandsPacket;
use pocketmine\network\bedrock\protocol\DataPacket;
use pocketmine\network\bedrock\protocol\LevelEventPacket;
use pocketmine\network\bedrock\protocol\LevelSoundEventPacket;
use pocketmine\network\bedrock\protocol\ProtocolInfo;
use pocketmine\network\bedrock\protocol\UpdateBlockPacket;
use pocketmine\utils\Binary;
use function property_exists;

class Protocol486Adapter extends Protocol503Adapter{
	public const PROTOCOL_VERSION = 486;

	protected const PACKET_MAP = [
		ProtocolInfo486::ADD_PLAYER_PACKET => AddPlayerPacket486::class,
		ProtocolInfo486::LOGIN_PACKET => LoginPacket486::class,
		ProtocolInfo486::START_GAME_PACKET => StartGamePacket486::class,
		ProtocolInfo486::INVENTORY_CONTENT_PACKET => InventoryContentPacket486::class,
		ProtocolInfo486::INVENTORY_SLOT_PACKET => InventorySlotPacket486::class,
		ProtocolInfo486::INVENTORY_TRANSACTION_PACKET => InventoryTransactionPacket486::class,
		ProtocolInfo486::ADD_ITEM_ACTOR_PACKET => AddItemActorPacket486::class,
		ProtocolInfo486::CRAFTING_DATA_PACKET => CraftingDataPacket486::class,
		ProtocolInfo486::CRAFTING_EVENT_PACKET => CraftingEventPacket486::class,
		ProtocolInfo486::CREATIVE_CONTENT_PACKET => CreativeContentPacket486::class,
		ProtocolInfo486::MOB_ARMOR_EQUIPMENT_PACKET => MobArmorEquipmentPacket486::class,
		ProtocolInfo486::MOB_EQUIPMENT_PACKET => MobEquipmentPacket486::class,
		ProtocolInfo486::SPAWN_PARTICLE_EFFECT_PACKET => SpawnParticleEffectPacket486::class,
	];

	protected const NEW_PACKETS = [
		ProtocolInfo::TICKING_AREAS_LOAD_STATUS_PACKET => true,
		ProtocolInfo::DIMENSION_DATA_PACKET => true,
		ProtocolInfo::AGENT_ACTION_EVENT_PACKET => true,
		ProtocolInfo::CHANGE_MOB_PROPERTY_PACKET => true,
	];

	public function __construct(){
		BlockPalette486::lazyInit();
		ItemPalette486::lazyInit();
	}

	public function processClientToServer(string $buf) : ?DataPacket{
		$offset = 0;
		$pid = Binary::readUnsignedVarInt($buf, $offset);

		if(isset(self::PACKET_MAP[$pid])){
			$class = self::PACKET_MAP[$pid];

			return new $class($buf);
		}

		return parent::processClientToServer($buf);
	}

	public function processServerToClient(DataPacket $packet) : ?DataPacket{
		$pid = $packet->pid();

		if(isset(self::NEW_PACKETS[$pid])){
			return null;
		}

		if(isset(self::PACKET_MAP[$pid])){
			$class = self::PACKET_MAP[$pid];

			$pk = new $class();
			foreach($packet as $k => $v){
				if($k === "isEncoded" or $k === "wasDecoded" or $k === "buffer" or $k === "offset"){
					continue;
				}

				if(property_exists($pk, $k)){
					$pk->{$k} = $v;
				}
			}

			return $pk;
		}

		return parent::processServerToClient($packet);
	}

	public function translateCommandParamType(int $paramType): ?int{
		static $types = [
			AvailableCommandsPacket::ARG_TYPE_WILDCARD_TARGET => AvailableCommandsPacket486::ARG_TYPE_WILDCARD_TARGET,
			AvailableCommandsPacket::ARG_TYPE_STRING => AvailableCommandsPacket486::ARG_TYPE_STRING,
			AvailableCommandsPacket::ARG_TYPE_INT_POSITION => AvailableCommandsPacket486::ARG_TYPE_BLOCK_POSITION,
			AvailableCommandsPacket::ARG_TYPE_POSITION => AvailableCommandsPacket486::ARG_TYPE_POSITION,
			AvailableCommandsPacket::ARG_TYPE_MESSAGE => AvailableCommandsPacket486::ARG_TYPE_MESSAGE,
			AvailableCommandsPacket::ARG_TYPE_RAWTEXT => AvailableCommandsPacket486::ARG_TYPE_RAWTEXT,
			AvailableCommandsPacket::ARG_TYPE_JSON => AvailableCommandsPacket486::ARG_TYPE_JSON,
			AvailableCommandsPacket::ARG_TYPE_COMMAND => AvailableCommandsPacket486::ARG_TYPE_COMMAND,
		];

		return $types[$paramType] ?? null;
	}

	public function translateBlockId(int $runtimeId) : int{
		BlockPalette::getLegacyFromRuntimeId($runtimeId, $id, $meta);
		return BlockPalette486::getRuntimeFromLegacyId($id, $meta);
	}

	protected function processBlocks(DataPacket $packet, bool $clientToServer) : void{
		if($clientToServer){
			$c = function(int $runtimeId) : int{
				BlockPalette486::getLegacyFromRuntimeId($runtimeId, $id, $meta);
				return BlockPalette::getRuntimeFromLegacyId($id, $meta);
			};

			if(!$packet->wasDecoded){
				$packet->decode();
			}
		}else{
			$c = Closure::fromCallable([$this, "translateBlockId"]);
		}

		if($packet instanceof LevelSoundEventPacket){
			if($packet->sound === LevelSoundEventPacket::SOUND_HIT or $packet->sound === LevelSoundEventPacket::SOUND_PLACE){
				$packet->extraData = $c($packet->extraData);
			}
		}elseif($packet instanceof UpdateBlockPacket){
			$packet->blockRuntimeId = $c($packet->blockRuntimeId);
		}elseif($packet instanceof LevelEventPacket){
			if($packet->evid === LevelEventPacket::EVENT_PARTICLE_PUNCH_BLOCK){
				$face = $packet->data >> 24;
				$packet->data = $c($packet->data & 0xffffff) | ($face << 24);
			}elseif($packet->evid === LevelEventPacket::EVENT_PARTICLE_DESTROY){
				$packet->data = $c($packet->data);
			}
		}
	}

	public function getChunkProtocol() : int{
		return self::PROTOCOL_VERSION;
	}

	public function getProtocolVersion() : int{
		return self::PROTOCOL_VERSION;
	}
}