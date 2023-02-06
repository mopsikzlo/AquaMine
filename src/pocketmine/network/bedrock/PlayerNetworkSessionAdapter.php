<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock;

use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\Timings;
use pocketmine\network\bedrock\protocol\AdventureSettingsPacket;
use pocketmine\network\bedrock\protocol\AnimatePacket;
use pocketmine\network\bedrock\protocol\BlockActorDataPacket;
use pocketmine\network\bedrock\protocol\BlockPickRequestPacket;
use pocketmine\network\bedrock\protocol\ClientToServerHandshakePacket;
use pocketmine\network\bedrock\protocol\CommandRequestPacket;
use pocketmine\network\bedrock\protocol\ContainerClosePacket;
use pocketmine\network\bedrock\protocol\ContainerOpenPacket;
use pocketmine\network\bedrock\protocol\DataPacket;
use pocketmine\network\bedrock\protocol\ActorEventPacket;
use pocketmine\network\bedrock\protocol\EmotePacket;
use pocketmine\network\bedrock\protocol\InteractPacket;
use pocketmine\network\bedrock\protocol\InventoryTransactionPacket;
use pocketmine\network\bedrock\protocol\ItemFrameDropItemPacket;
use pocketmine\network\bedrock\protocol\LevelSoundEventPacket;
use pocketmine\network\bedrock\protocol\LoginPacket;
use pocketmine\network\bedrock\protocol\MobEquipmentPacket;
use pocketmine\network\bedrock\protocol\MovePlayerPacket;
use pocketmine\network\bedrock\protocol\PacketViolationWarningPacket;
use pocketmine\network\bedrock\protocol\PlayerActionPacket;
use pocketmine\network\bedrock\protocol\PlayerSkinPacket;
use pocketmine\network\bedrock\protocol\ResourcePackChunkRequestPacket;
use pocketmine\network\bedrock\protocol\ResourcePackClientResponsePacket;
use pocketmine\network\bedrock\protocol\RequestChunkRadiusPacket;
use pocketmine\network\bedrock\protocol\RespawnPacket;
use pocketmine\network\bedrock\protocol\SetLocalPlayerAsInitializedPacket;
use pocketmine\network\bedrock\protocol\SetPlayerGameTypePacket;
use pocketmine\network\bedrock\protocol\TextPacket;
use pocketmine\BedrockPlayer;
use pocketmine\network\bedrock\protocol\types\inventory\ContainerIds;
use pocketmine\network\bedrock\protocol\types\inventory\WindowTypes;
use pocketmine\Server;

use function bin2hex;
use function strlen;
use function substr;

class PlayerNetworkSessionAdapter extends BedrockNetworkSession{

	/** @var Server */
	private $server;
	/** @var BedrockPlayer */
	private $player;

    private $lastTextPacket = 0;
    private $textPacketCnt = 0;
    private $textPacketExceed = 0;

	public function __construct(Server $server, BedrockPlayer $player){
		$this->server = $server;
		$this->player = $player;
	}

	public function handleDataPacket(DataPacket $packet){
		//TODO: Remove this hack once InteractPacket spam issue is fixed
		if(strlen($packet->buffer) > 1 and substr($packet->buffer, 0, 2) === "\x21\x04"){
			return;
		}
		if((!$this->player->loggedIn and !$this->player->awaitingEncryptionHandshake) and !($packet instanceof LoginPacket)){ //Ignore any packets before login
 			return;
		}

		$timings = Timings::getReceiveDataPacketTimings($packet);
		$timings->startTiming();

		if(!$packet->wasDecoded and $packet->mustBeDecoded()){ //Allow plugins to decode it
			$packet->decode();
			if(!$packet->feof() and !$packet->mayHaveUnreadBytes()){
				$remains = substr($packet->buffer, $packet->offset);
				$this->server->getLogger()->debug("Still " . strlen($remains) . " bytes unread in " . $packet->getName() . ": 0x" . bin2hex($remains));
			}
		}

		$ev = new DataPacketReceiveEvent($this->player, $packet);
		$ev->call();

		if(!$ev->isCancelled() and $packet->mustBeDecoded() and !$packet->handle($this)){
			$this->server->getLogger()->debug("Unhandled " . $packet->getName() . " received from " . $this->player->getName() . ": 0x" . bin2hex($packet->buffer));
		}

		$timings->stopTiming();
		return;
	}

	public function handleLogin(LoginPacket $packet) : bool{
		return $this->player->handleBedrockLogin($packet);
	}

	public function handleClientToServerHandshake(ClientToServerHandshakePacket $packet) : bool{
		return $this->player->onEncryptionHandshake();
	}

	public function handleResourcePackClientResponse(ResourcePackClientResponsePacket $packet) : bool{
		return $this->player->handleBedrockResourcePackClientResponse($packet);
	}

	public function handleResourcePackChunkRequest(ResourcePackChunkRequestPacket $packet) : bool{
		return $this->player->handleBedrockResourcePackChunkRequest($packet);
	}

	public function handleRequestChunkRadius(RequestChunkRadiusPacket $packet) : bool{
		if(!$this->player->loginProcessed){
			return false;
		}
		$this->player->setViewDistance($packet->radius);

		return true;
	}

	public function handleSetLocalPlayerAsInitialized(SetLocalPlayerAsInitializedPacket $packet) : bool{
		$this->player->doFirstSpawn();

		return true;
	}

	public function handleMovePlayer(MovePlayerPacket $packet) : bool{
		$yaw = fmod($packet->yaw, 360);
		$pitch = fmod($packet->pitch, 360);
		if($yaw < 0){
			$yaw += 360;
		}

		$this->player->setRotation($yaw, $pitch);
		$this->player->updateNextPosition($packet->position->round(4)->subtract(0, 1.62, 0));

		return true;
	}

	public function handleInventoryTransaction(InventoryTransactionPacket $packet) : bool{
		return $this->player->handleInventoryTransaction($packet);
	}

	public function handleLevelSoundEvent(LevelSoundEventPacket $packet) : bool{
		return $this->player->handleBedrockLevelSoundEvent($packet);
	}

	public function handleActorEvent(ActorEventPacket $packet) : bool{
		return $this->player->handleActorEvent($packet);
	}

	public function handleMobEquipment(MobEquipmentPacket $packet) : bool{
		return $this->player->handleBedrockMobEquipment($packet);
	}

	public function handleBlockPickRequest(BlockPickRequestPacket $packet) : bool{
		return $this->player->handleBedrockBlockPickRequest($packet);
	}

	public function handleAnimate(AnimatePacket $packet) : bool{
		return $this->player->handleBedrockAnimate($packet);
	}

	public function handleContainerClose(ContainerClosePacket $packet) : bool{
		if(!$this->player->spawned){
			return true;
		}

		$pk = new ContainerClosePacket();
		$pk->windowId = $packet->windowId;
		$pk->server = false;
		$this->player->sendDataPacket($pk);

		$this->player->newInventoryClose($packet->windowId);

		if($packet->windowId !== ContainerIds::INVENTORY){
			$this->player->setClientClosingWindowId($packet->windowId);
			$this->player->closeWindow($packet->windowId);
			$this->player->setClientClosingWindowId(-1);
		}

		return true;
	}

	public function handleAdventureSettings(AdventureSettingsPacket $packet) : bool{
		$this->player->toggleFlight($packet->getFlag(AdventureSettingsPacket::FLYING));
		$this->player->toggleNoClip($packet->getFlag(AdventureSettingsPacket::NO_CLIP));

		return true;
	}

	public function handleBlockActorData(BlockActorDataPacket $packet) : bool{
		return $this->player->handleBedrockBlockActorData($packet);
	}

	public function handleSetPlayerGameType(SetPlayerGameTypePacket $packet) : bool{
		return $this->player->handleBedrockSetPlayerGameType($packet);
	}

	public function handleItemFrameDropItem(ItemFrameDropItemPacket $packet) : bool{
		return $this->player->handleBedrockItemFrameDropItem($packet);
	}

	public function handleCommandRequest(CommandRequestPacket $packet) : bool{
		if(!$this->player->spawned or !$this->player->isAlive()){
			return true;
		}

		$this->player->chat($packet->command);
		return true;
	}

	public function handleText(TextPacket $packet) : bool{
        $time = time();

        if($this->lastTextPacket !== $time){
            $this->textPacketCnt = 0;
        }
        $this->lastTextPacket = $time;

        if(++$this->textPacketCnt >= 5){
            if(++$this->textPacketExceed >= 10){
                $this->server->getNetwork()->blockAddress($this->player->getAddress(), 300);
            }
            return false;
        }

        if(strlen($packet->message) > 200){
            $this->server->getLogger()->warning('big text packet from '.$this->player->getName().' as '.strlen($packet->message).' len with textPacketCnt='.$this->textPacketCnt);
            $this->server->getNetwork()->blockAddress($this->player->getAddress(), 300);

            return false;
        }

        if(!$this->player->spawned or !$this->player->isAlive()){
			return true;
		}

		$this->player->resetCrafting();
		if($packet->type === TextPacket::TYPE_CHAT){
			$this->player->chat($packet->message);
		}

		return true;
	}

	public function handlePlayerAction(PlayerActionPacket $packet) : bool{
		return $this->player->handleBedrockPlayerAction($packet);
	}

	public function handlePlayerSkin(PlayerSkinPacket $packet) : bool{
		return $this->player->handlePlayerSkin($packet);
	}

	public function handleRespawn(RespawnPacket $packet) : bool{
		return $this->player->handleBedrockRespawn($packet);
	}

	public function handleInteract(InteractPacket $packet) : bool{
		if($packet->action === InteractPacket::ACTION_OPEN_INVENTORY and $packet->actorRuntimeId === $this->player->getId()){
			if($this->player->newInventoryOpen(ContainerIds::INVENTORY)){
				$pk = new ContainerOpenPacket();
				$pk->windowId = ContainerIds::INVENTORY;
				$pk->type = WindowTypes::INVENTORY;
				$pk->x = $pk->y = $pk->z = 0;
				$this->player->sendDataPacket($pk);
			}
		}

		return true;
	}

	public function handlePacketViolationWarning(PacketViolationWarningPacket $packet) : bool{
		$this->player->getServer()->getLogger()->notice("PacketViolationWarning from {$this->player->getName()}: (type={$packet->type},severity={$packet->severity},packetId={$packet->packetId},violationContext={$packet->violationContext})");
		return true;
	}

	public function handleEmote(EmotePacket $packet) : bool{
		return $this->player->handleEmote($packet);
	}
}
