<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\adapter\v407\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\bedrock\adapter\v407\palette\BlockPalette;
use pocketmine\network\bedrock\adapter\v407\palette\ItemPalette;
use pocketmine\network\bedrock\protocol\types\PlayerMovementSettings;
use pocketmine\network\bedrock\protocol\types\PlayerMovementType;

class StartGamePacket extends \pocketmine\network\bedrock\adapter\v431\protocol\StartGamePacket {
	use PacketTrait;

	public function decodePayload(){
		$this->actorUniqueId = $this->getActorUniqueId();
		$this->actorRuntimeId = $this->getActorRuntimeId();
		$this->playerGamemode = $this->getVarInt();

		$this->playerPosition = $this->getVector3();

		$this->pitch = $this->getLFloat();
		$this->yaw = $this->getLFloat();

		//Level settings
		$this->seed = $this->getVarInt();
		$this->spawnBiomeType = $this->getLShort();
		$this->userDefinedBiomeName = $this->getString();
		$this->dimension = $this->getVarInt();
		$this->generator = $this->getVarInt();
		$this->worldGamemode = $this->getVarInt();
		$this->difficulty = $this->getVarInt();
		$this->getBlockPosition($this->spawnX, $this->spawnY, $this->spawnZ);
		$this->hasAchievementsDisabled = $this->getBool();
		$this->time = $this->getVarInt();
		$this->eduEditionOffer = $this->getVarInt();
		$this->hasEduFeaturesEnabled = $this->getBool();
		$this->educationProductId = $this->getString();
		$this->rainLevel = $this->getLFloat();
		$this->lightningLevel = $this->getLFloat();
		$this->hasConfirmedPlatformLockedContent = $this->getBool();
		$this->isMultiplayerGame = $this->getBool();
		$this->hasLANBroadcast = $this->getBool();
		$this->xboxLiveBroadcastIntent = $this->getVarInt();
		$this->platformBroadcastIntent = $this->getVarInt();
		$this->commandsEnabled = $this->getBool();
		$this->isTexturePacksRequired = $this->getBool();
		$this->gameRules = $this->getGameRules();
		$this->hasBonusChestEnabled = $this->getBool();
		$this->hasStartWithMapEnabled = $this->getBool();
		$this->defaultPlayerPermission = $this->getVarInt();
		$this->serverChunkTickRadius = $this->getLInt();
		$this->hasLockedBehaviorPack = $this->getBool();
		$this->hasLockedResourcePack = $this->getBool();
		$this->isFromLockedWorldTemplate = $this->getBool();
		$this->useMsaGamertagsOnly = $this->getBool();
		$this->isFromWorldTemplate = $this->getBool();
		$this->isWorldTemplateOptionLocked = $this->getBool();
		$this->onlySpawnV1Villagers = $this->getBool();
		$this->getString(); // vanilla version
		$this->vanillaVersion = ProtocolInfo::MINECRAFT_VERSION_NETWORK;
		$this->limitedWorldWidth = $this->getLInt();
		$this->limitedWorldDepth = $this->getLInt();
		$this->newNether = $this->getBool();
		$this->isExperimentalGameplayForced = $this->getBool();
		if($this->isExperimentalGameplayForced){
			$this->forceExperimentalGameplay = $this->getBool();
		}

		$this->levelId = $this->getString();
		$this->worldName = $this->getString();
		$this->premiumWorldTemplateId = $this->getString();
		$this->isTrial = $this->getBool();
		$movementType = $this->getBool() ? PlayerMovementType::SERVER_AUTHORITATIVE_V1 : PlayerMovementType::LEGACY;
		$this->playerMovementSettings = new PlayerMovementSettings($movementType, 0, false);
		$this->currentTick = $this->getLLong();

		$this->enchantmentSeed = $this->getVarInt();

		$this->getNbtRoot(); //block palette

		$count = $this->getUnsignedVarInt(); //item palette
		for($i = 0; $i < $count; ++$i){
			$this->getString(); // string ID
			$this->getSignedLShort(); // numeric ID
		}

		$this->multiplayerCorrelationId = $this->getString();
		$this->isInventoryServerAuthoritative = $this->getBool();
	}

	public function encodePayload(){
		$this->putActorUniqueId($this->actorUniqueId);
		$this->putActorRuntimeId($this->actorRuntimeId);
		$this->putVarInt($this->playerGamemode);

		$this->putVector3($this->playerPosition);

		$this->putLFloat($this->pitch);
		$this->putLFloat($this->yaw);

		//Level settings
		$this->putVarInt($this->seed);
		$this->putLShort($this->spawnBiomeType);
		$this->putString($this->userDefinedBiomeName);
		$this->putVarInt($this->dimension);
		$this->putVarInt($this->generator);
		$this->putVarInt($this->worldGamemode);
		$this->putVarInt($this->difficulty);
		$this->putBlockPosition($this->spawnX, $this->spawnY, $this->spawnZ);
		$this->putBool($this->hasAchievementsDisabled);
		$this->putVarInt($this->time);
		$this->putVarInt($this->eduEditionOffer);
		$this->putBool($this->hasEduFeaturesEnabled);
		$this->putString($this->educationProductId);
		$this->putLFloat($this->rainLevel);
		$this->putLFloat($this->lightningLevel);
		$this->putBool($this->hasConfirmedPlatformLockedContent);
		$this->putBool($this->isMultiplayerGame);
		$this->putBool($this->hasLANBroadcast);
		$this->putVarInt($this->xboxLiveBroadcastIntent);
		$this->putVarInt($this->platformBroadcastIntent);
		$this->putBool($this->commandsEnabled);
		$this->putBool($this->isTexturePacksRequired);
		$this->putGameRules($this->gameRules);
		$this->putBool($this->hasBonusChestEnabled);
		$this->putBool($this->hasStartWithMapEnabled);
		$this->putVarInt($this->defaultPlayerPermission);
		$this->putLInt($this->serverChunkTickRadius);
		$this->putBool($this->hasLockedBehaviorPack);
		$this->putBool($this->hasLockedResourcePack);
		$this->putBool($this->isFromLockedWorldTemplate);
		$this->putBool($this->useMsaGamertagsOnly);
		$this->putBool($this->isFromWorldTemplate);
		$this->putBool($this->isWorldTemplateOptionLocked);
		$this->putBool($this->onlySpawnV1Villagers);
		$this->putString(ProtocolInfo::MINECRAFT_VERSION_NETWORK); // vanilla version
		$this->putLInt($this->limitedWorldWidth);
		$this->putLInt($this->limitedWorldDepth);
		$this->putBool($this->newNether);
		$this->putBool($this->isExperimentalGameplayForced);
		if($this->isExperimentalGameplayForced){
			$this->putBool($this->forceExperimentalGameplay);
		}
		$this->putString($this->levelId);
		$this->putString($this->worldName);
		$this->putString($this->premiumWorldTemplateId);
		$this->putBool($this->isTrial);
		$this->putBool($this->playerMovementSettings->getMovementType() !== PlayerMovementType::LEGACY);
		$this->putLLong($this->currentTick);

		$this->putVarInt($this->enchantmentSeed);

		$this->put(BlockPalette::getEncodedPalette());
		$this->put(ItemPalette::getEncodedPalette());

		$this->putString($this->multiplayerCorrelationId);
		$this->putBool($this->isInventoryServerAuthoritative);
	}
}