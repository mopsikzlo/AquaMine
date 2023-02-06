<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>


use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\TreeRoot;
use pocketmine\network\bedrock\palette\ItemPalette;
use pocketmine\network\bedrock\protocol\types\EducationUriResource;
use pocketmine\network\bedrock\protocol\types\Experiments;
use pocketmine\network\bedrock\protocol\types\PlayerMovementSettings;
use pocketmine\network\bedrock\protocol\types\PlayerMovementType;
use pocketmine\network\bedrock\protocol\types\PlayerPermissions;
use pocketmine\network\mcpe\NetworkNbtSerializer;
use pocketmine\network\NetworkSession;
use pocketmine\utils\UUID;

class StartGamePacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::START_GAME_PACKET;

	public const GAME_PUBLISH_SETTING_NO_MULTI_PLAY = 0;
	public const GAME_PUBLISH_SETTING_INVITE_ONLY = 1;
	public const GAME_PUBLISH_SETTING_FRIENDS_ONLY = 2;
	public const GAME_PUBLISH_SETTING_FRIENDS_OF_FRIENDS = 3;
	public const GAME_PUBLISH_SETTING_PUBLIC = 4;

	public const SPAWN_BIOME_TYPE_DEFAULT = 0;
	public const SPAWN_BIOME_TYPE_USER_DEFINED = 1;

	/** @var int */
	public $actorUniqueId;
	/** @var int */
	public $actorRuntimeId;
	/** @var int */
	public $playerGamemode;

	/** @var Vector3 */
	public $playerPosition;

	/** @var float */
	public $pitch;
	/** @var float */
	public $yaw;

	/** @var int */
	public $seed;
	/** @var int */
	public $spawnBiomeType = self::SPAWN_BIOME_TYPE_DEFAULT;
	/** @var string */
	public $userDefinedBiomeName = "";
	/** @var int */
	public $dimension;
	/** @var int */
	public $generator = 1; //default infinite - 0 old, 1 infinite, 2 flat
	/** @var int */
	public $worldGamemode;
	/** @var int */
	public $difficulty;
	/** @var int */
	public $spawnX;
	/** @var int */
	public $spawnY;
	/** @var int */
	public $spawnZ;
	/** @var bool */
	public $hasAchievementsDisabled = true;
	/** @var int */
	public $time = -1;
	/** @var int */
	public $eduEditionOffer = 0;
	/** @var bool */
	public $hasEduFeaturesEnabled = false;
	/** @var string */
	public $educationProductId = "";
	/** @var float */
	public $rainLevel;
	/** @var float */
	public $lightningLevel;
	/** @var bool */
	public $hasConfirmedPlatformLockedContent = false;
	/** @var bool */
	public $isMultiplayerGame = true;
	/** @var bool */
	public $hasLANBroadcast = true;
	/** @var int */
	public $xboxLiveBroadcastIntent = self::GAME_PUBLISH_SETTING_PUBLIC;
	/** @var int */
	public $platformBroadcastIntent = self::GAME_PUBLISH_SETTING_PUBLIC;
	/** @var bool */
	public $commandsEnabled;
	/** @var bool */
	public $isTexturePacksRequired = true;
	/** @var array */
	public $gameRules = [ //TODO: implement this
		"naturalregeneration" => [1, false, false] //Hack for client side regeneration
	];
	/** @var Experiments */
	public $experiments;
	/** @var bool */
	public $hasBonusChestEnabled = false;
	/** @var bool */
	public $hasStartWithMapEnabled = false;
	/** @var int */
	public $defaultPlayerPermission = PlayerPermissions::MEMBER; //TODO
	/** @var int */
	public $serverChunkTickRadius = 4; //TODO (leave as default for now)
	/** @var bool */
	public $hasLockedBehaviorPack = false;
	/** @var bool */
	public $hasLockedResourcePack = false;
	/** @var bool */
	public $isFromLockedWorldTemplate = false;
	/** @var bool */
	public $useMsaGamertagsOnly = false;
	/** @var bool */
	public $isFromWorldTemplate = false;
	/** @var bool */
	public $isWorldTemplateOptionLocked = false;
	/** @var bool */
	public $onlySpawnV1Villagers = false;
	/** @var string */
	public $vanillaVersion = ProtocolInfo::MINECRAFT_VERSION_NETWORK;
	/** @var int */
	public $limitedWorldWidth = 0;
	/** @var int */
	public $limitedWorldDepth = 0;
	/** @var bool */
	public $newNether = false;
    /** @var EducationUriResource|null */
    public $eduSharedUriResource = null;
    /** @var bool */
	public $isExperimentalGameplayForced = false;
	/** @var bool */
	public $forceExperimentalGameplay = false;

	/** @var string */
	public $levelId = ""; //base64 string, usually the same as world folder name in vanilla
	/** @var string */
	public $worldName;
	/** @var string */
	public $premiumWorldTemplateId = "";
	/** @var bool */
	public $isTrial = false;
	/** @var PlayerMovementSettings */
	public $playerMovementSettings;
	/** @var int */
	public $currentTick = 0; //only used if isTrial is true
	/** @var int */
	public $enchantmentSeed = 0;
	/** @var string */
	public $multiplayerCorrelationId = ""; //TODO: this should be filled with a UUID of some sort
	/** @var bool */
	public $isInventoryServerAuthoritative = false;
    /** @var string */
    public $serverSoftwareVersion;
	/** @var CompoundTag */
	public $playerActorProperties;
	/** @var UUID */
	public $worldTemplateId;

	public function decodePayload(){
		$this->actorUniqueId = $this->getActorUniqueId();
		$this->actorRuntimeId = $this->getActorRuntimeId();
		$this->playerGamemode = $this->getVarInt();

		$this->playerPosition = $this->getVector3();

		$this->pitch = $this->getLFloat();
		$this->yaw = $this->getLFloat();

		//Level settings
		$this->seed = $this->getLLong();
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
		$this->experiments = $this->getExperiments();
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
		$this->vanillaVersion = $this->getString();
		$this->limitedWorldWidth = $this->getLInt();
		$this->limitedWorldDepth = $this->getLInt();
		$this->newNether = $this->getBool();
        $this->eduSharedUriResource = EducationUriResource::read($this);
        $this->isExperimentalGameplayForced = $this->getBool();
		if($this->isExperimentalGameplayForced){
			$this->forceExperimentalGameplay = $this->getBool();
		}

		$this->levelId = $this->getString();
		$this->worldName = $this->getString();
		$this->premiumWorldTemplateId = $this->getString();
		$this->isTrial = $this->getBool();
		$this->playerMovementSettings = PlayerMovementSettings::read($this);
		$this->currentTick = $this->getLLong();

		$this->enchantmentSeed = $this->getVarInt();

		$count = $this->getUnsignedVarInt(); //block palette
		for($i = 0; $i < $count; ++$i){
			$this->getString(); // string ID
			$this->getNbtCompoundRoot(); // tag
		}

		$count = $this->getUnsignedVarInt(); //item palette
		for($i = 0; $i < $count; ++$i){
			$this->getString(); // string ID
			$this->getSignedLShort(); // numeric ID
			$this->getBool(); // is component based
		}

		$this->multiplayerCorrelationId = $this->getString();
		$this->isInventoryServerAuthoritative = $this->getBool();
        $this->serverSoftwareVersion = $this->getString();
		$this->playerActorProperties = $this->getNbtCompoundRoot();
		$this->getLLong(); // block palette checksum
		$this->worldTemplateId = $this->getUUID();
	}

	public function encodePayload(){
		$this->putActorUniqueId($this->actorUniqueId);
		$this->putActorRuntimeId($this->actorRuntimeId);
		$this->putVarInt($this->playerGamemode);

		$this->putVector3($this->playerPosition);

		$this->putLFloat($this->pitch);
		$this->putLFloat($this->yaw);

		//Level settings
		$this->putLLong($this->seed);
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
		$this->putExperiments($this->experiments);
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
		$this->putString($this->vanillaVersion);
		$this->putLInt($this->limitedWorldWidth);
		$this->putLInt($this->limitedWorldDepth);
		$this->putBool($this->newNether);
        ($this->eduSharedUriResource ?? new EducationUriResource("", ""))->write($this);

        $this->putBool($this->isExperimentalGameplayForced);
		if($this->isExperimentalGameplayForced){
			$this->putBool($this->forceExperimentalGameplay);
		}
		$this->putString($this->levelId);
		$this->putString($this->worldName);
		$this->putString($this->premiumWorldTemplateId);
		$this->putBool($this->isTrial);
		$this->playerMovementSettings->write($this);
		$this->putLLong($this->currentTick);

		$this->putVarInt($this->enchantmentSeed);

		$this->putUnsignedVarInt(0); // Custom block count
		$this->put(ItemPalette::getEncodedPalette());

		$this->putString($this->multiplayerCorrelationId);
		$this->putBool($this->isInventoryServerAuthoritative);
        $this->putString($this->serverSoftwareVersion);

		$nbt = new NetworkNbtSerializer();
		$this->put($nbt->write(new TreeRoot($this->playerActorProperties)));

		$this->putLLong(0); // block palette checksum

		$this->putUUID($this->worldTemplateId);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleStartGame($this);
	}
}
