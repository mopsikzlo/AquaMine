<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe;

use pocketmine\BedrockPlayer;
use pocketmine\event\player\PlayerCreationEvent;
use pocketmine\network\AdvancedNetworkInterface;
use pocketmine\network\bedrock\BedrockPacketBatch;
use pocketmine\network\bedrock\BedrockPong;
use pocketmine\network\bedrock\NetworkCompression as BedrockNetworkCompression;
use pocketmine\network\bedrock\protocol\PacketPool as BedrockPacketPool;
use pocketmine\network\bedrock\protocol\ProtocolInfo as BedrockProtocolInfo;
use pocketmine\network\mcpe\encryption\DecryptionException;
use pocketmine\network\mcpe\protocol\BatchPacket;
use pocketmine\network\mcpe\protocol\DataPacket;
use pocketmine\network\mcpe\protocol\PacketPool;
use pocketmine\network\Network;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\snooze\SleeperNotifier;
use raklib\protocol\EncapsulatedPacket;
use raklib\protocol\PacketReliability;
use raklib\RakLib;
use raklib\server\RakLibServer;
use raklib\server\ServerHandler;
use raklib\server\ServerInstance;
use raklib\utils\InternetAddress;
use function bin2hex;
use function get_class;
use function igbinary_unserialize;
use function microtime;
use function ord;
use function spl_object_id;
use function substr;
use const PTHREADS_INHERIT_CONSTANTS;

class RakLibInterface implements ServerInstance, AdvancedNetworkInterface{

	/**
	 * Sometimes this gets changed when the MCPE-layer protocol gets broken to the point where old and new can't
	 * communicate. It's important that we check this to avoid catastrophes.
	 */
	private const MCPE_RAKNET_PROTOCOL_VERSION = 8;
	private const BEDROCK_RAKNET_PROTOCOL_VERSION = 10;

	private const MCPE_RAKNET_PACKET_ID = "\xfe";

	private const PONG_DATA_UPDATE_RATE = 1.0;

	/** @var Server */
	private $server;

	/** @var Network */
	private $network;

	/** @var RakLibServer */
	private $rakLib;

	/** @var int */
	private $port;

	/** @var McpePong */
	private $pongData;

	/** @var Player[] */
	private $players = [];

	/** @var int[] */
	private $identifiers = [];

	/** @var int[] */
	private $identifiersACK = [];

	/** @var bool[] */
	private $ignorePing = [];

	/** @var ServerHandler */
	private $interface;

	/** @var SleeperNotifier */
	private $sleeper;

	/** @var float */
	private $lastPongDataUpdate = 0.0;

	public function __construct(Server $server, ?int $port = null){
		$this->server = $server;
		$this->port = $port ?? $server->getPort();

		$this->sleeper = new SleeperNotifier();
		$this->rakLib = new RakLibServer($server->getLogger(), $server->getLoader(), new InternetAddress($server->getIp() === "" ? "0.0.0.0" : $server->getIp(), $this->port, 4), (int) $server->getProperty("network.max-mtu-size", 1492), [self::MCPE_RAKNET_PROTOCOL_VERSION, self::BEDROCK_RAKNET_PROTOCOL_VERSION], $this->sleeper);
		$this->interface = new ServerHandler($this->rakLib, $this);

		$this->setPongData(new McpePong());
	}

	public function start() : void{
		$this->server->getTickSleeper()->addNotifier($this->sleeper, function() : void{
			$this->process();
		});
		$this->server->getLogger()->debug("Waiting for RakLib to start...");
		$this->rakLib->startAndWait(PTHREADS_INHERIT_CONSTANTS); //HACK: MainLogger needs constants for exception logging
		$this->server->getLogger()->debug("RakLib booted successfully");

		$this->setPortChecking($this->server->getAdvancedProperty("network.port-checking", true));
		$this->setPacketLimit($this->server->getAdvancedProperty("network.packet-limit", 300));
	}

	public function setNetwork(Network $network) : void{
		$this->network = $network;
	}

	public function process() : bool{
		$work = false;
		if($this->interface->handlePacket()){
			$work = true;
			while($this->interface->handlePacket()){
			}
		}

		if(microtime(true) - $this->lastPongDataUpdate > self::PONG_DATA_UPDATE_RATE){
			$this->updatePongData();
		}

		if(!$this->rakLib->isRunning() and !$this->rakLib->isShutdown()){
			$this->network->unregisterInterface($this);

			$e = $this->rakLib->getCrashInfo();
			if($e !== null){
				throw $e;
			}
			throw new \Exception("RakLib Thread crashed without crash information");
		}

		return $work;
	}

	public function closeSession(int $sessionId, string $reason) : void{
		if(isset($this->players[$sessionId])){
			$player = $this->players[$sessionId];
			unset($this->identifiers[spl_object_id($player)]);
			unset($this->players[$sessionId]);
			unset($this->identifiersACK[$sessionId]);
			unset($this->ignorePing[$sessionId]);
			$player->close($player->getLeaveMessage(), $reason);
		}
	}

	public function close(Player $player, string $reason = "unknown reason") : void{
		if(isset($this->identifiers[$h = spl_object_id($player)])){
			unset($this->players[$this->identifiers[$h]]);
			unset($this->identifiersACK[$this->identifiers[$h]]);
			unset($this->ignorePing[$this->identifiers[$h]]);
			$this->interface->closeSession($this->identifiers[$h], $reason);
			unset($this->identifiers[$h]);
		}
	}

	public function shutdown() : void{
		$this->server->getTickSleeper()->removeNotifier($this->sleeper);
		$this->interface->shutdown();
	}

	public function emergencyShutdown() : void{
		$this->server->getTickSleeper()->removeNotifier($this->sleeper);
		$this->interface->emergencyShutdown();
	}

	public function openSession(int $sessionId, string $address, int $port, int $clientID, int $protocolVersion) : void{
		$cl = $protocolVersion === self::BEDROCK_RAKNET_PROTOCOL_VERSION ? BedrockPlayer::class : Player::class;
		$ev = new PlayerCreationEvent($this, $cl, $cl, null, $address, $port);
		$ev->call();
		$class = $ev->getPlayerClass();

		$player = new $class($this, $ev->getClientId(), $ev->getAddress(), $ev->getPort());
		$this->players[$sessionId] = $player;
		$this->identifiersACK[$sessionId] = 0;
		$this->identifiers[spl_object_id($player)] = $sessionId;
		$this->server->addPlayer($sessionId, $player);
	}

	public function handleEncapsulated(int $sessionId, EncapsulatedPacket $packet, int $flags) : void{
		if(isset($this->players[$sessionId])){
			$player = $this->players[$sessionId];

			try{
				if($packet->buffer !== ""){
					if($packet->buffer[0] !== self::MCPE_RAKNET_PACKET_ID){
						throw new \UnexpectedValueException("Unexpected non-FE packet");
					}
					$cipher = $player->getCipher();
					$buffer = substr($packet->buffer, 1);
					try {
						if($cipher !== null) {
							$buffer = $cipher->decrypt($buffer);
						}
					} catch (DecryptionException $e) {}
					if($player instanceof BedrockPlayer){
						if($packet->buffer[0] === BedrockProtocolInfo::MCPE_RAKNET_PACKET_ID){
							$protocolAdapter = $player->getProtocolAdapter();

							$stream = new BedrockPacketBatch(BedrockNetworkCompression::decompress($buffer));
							$count = 0;
							while(!$stream->feof()){
								if(++$count > 1024){
									throw new \UnexpectedValueException("Too many batched packets!");
								}

								$buf = $stream->getString();
								if($protocolAdapter !== null){
									$pk = $protocolAdapter->processClientToServer($buf);
								}else{
									$pk = BedrockPacketPool::getPacket($buf);
								}

								if($pk !== null){
									$player->handleDataPacket($pk);
								}
							}
						}
					} else {
						$pk = $this->getPacket(self::MCPE_RAKNET_PACKET_ID.$buffer);
						$player->handleDataPacket($pk);
					}
				}
			}catch(\Throwable $e){
				$logger = $this->server->getLogger();
				$logger->debug("Packet " . (isset($pk) ? get_class($pk) : "unknown") . " 0x" . bin2hex($packet->buffer));
				$logger->logException($e);

				$player->close($player->getLeaveMessage(), "Internal server error");
				$this->interface->blockAddress($player->getAddress(), 5);
			}
		}
	}

	public function blockAddress(string $address, int $timeout = 300) : void{
		$this->interface->blockAddress($address, $timeout);
	}

	public function handleRaw(string $address, int $port, string $payload) : void{
		$this->server->handlePacket($address, $port, $payload);
	}

	public function sendRawPacket(string $address, int $port, string $payload) : void{
		$this->interface->sendRaw($address, $port, $payload);
	}

	public function unlimitAddress(string $address) : void{
		$this->interface->unlimitAddress($address);
	}

	public function notifyACK(int $sessionId, int $identifierACK) : void{

	}

	public function updatePongData() : void{
		$info = $this->server->getQueryInformation();

		$this->pongData->setPlayerCount($info->getPlayerCount());
		$this->pongData->setMaxPlayerCount($info->getMaxPlayerCount());
		$this->pongData->setMotd($info->getMotd());
		$this->pongData->setSubMotd($info->getSubMotd());
		$this->pongData->setGameType(Server::getGamemodeName($this->server->getGamemode()));

		$this->interface->sendOption("name", $this->pongData->toServerName());

		$this->lastPongDataUpdate = microtime(true);
	}

	/**
	 * @return McpePong
	 */
	public function getPongData() : McpePong{
		return $this->pongData;
	}

	/**
	 * @param McpePong $pongData
	 */
	public function setPongData(McpePong $pongData) : void{
		$this->pongData = $pongData;

		if($pongData->getEdition() === ""){
			$pongData->setEdition("MCPE");
		}
		if($pongData->getServerId() === -1){
			$pongData->setProtocolVersion(BedrockProtocolInfo::CURRENT_PROTOCOL);
		}
		if($pongData->getMinecraftVersion() === ""){
			$pongData->setMinecraftVersion(BedrockProtocolInfo::MINECRAFT_VERSION_NETWORK);
		}
		if($pongData->getServerId() === -1){
			$pongData->setServerId($this->rakLib->getServerId());
		}

		if($pongData instanceof BedrockPong){
			if($pongData->getIpv4Port() === -1){
				$pongData->setIpv4Port($this->port);
			}
			if($pongData->getIpv6Port() === -1){
				$pongData->setIpv6Port($this->port);
			}
		}

		$this->updatePongData();
	}

	public function setPortChecking(bool $value) : void{
		$this->interface->sendOption("portChecking", $value);
	}

	public function setPacketLimit(int $packetLimit) : void{
		$this->interface->sendOption("packetLimit", $packetLimit);
	}

	public function handleOption(string $option, string $value) : void{
		if($option === "bandwidth"){
			$v = igbinary_unserialize($value);
			$this->network->addStatistics($v["up"], $v["down"]);
		}
	}

	public function ignorePing(Player $player, bool $value = true) : void{
		if(isset($this->identifiers[$h = spl_object_id($player)])){
			$sessionId = $this->identifiers[$h];

			if($value){
				$this->ignorePing[$sessionId] = true;
			}else{
				unset($this->ignorePing[$sessionId]);
			}
		}
	}

	public function updatePing(int $sessionId, int $pingMS) : void{
		if(isset($this->players[$sessionId]) and !isset($this->ignorePing[$sessionId])){
			$this->players[$sessionId]->setPing($pingMS);
		}
	}

	public function putPacket(Player $player, DataPacket $packet, bool $needACK = false, bool $immediate = true) : ?int{
		if(isset($this->identifiers[$h = spl_object_id($player)])){
			if(!$packet->isEncoded){
				$packet->encode();
			}

			if($packet instanceof BatchPacket){
				return $this->putBuffer($player, $packet->buffer, $needACK, $immediate);
			}else{
				$this->server->batchPackets([$player], [$packet], true, $immediate);
				return null;
			}
		}

		return null;
	}

	public function putBuffer(Player $player, string $buffer, bool $needACK = false, bool $immediate = true) : ?int{
		if(isset($this->identifiers[$h = spl_object_id($player)])){
			$sessionId = $this->identifiers[$h];

			$cipher = $player->getCipher();
			$rawBuffer = substr($buffer, 1);
			$buffer = self::MCPE_RAKNET_PACKET_ID . ($cipher !== null ? $cipher->encrypt($rawBuffer) : $rawBuffer);

			$pk = new EncapsulatedPacket();
			$pk->buffer = $buffer;
			$pk->reliability = $immediate ? PacketReliability::RELIABLE : PacketReliability::RELIABLE_ORDERED;
			$pk->orderChannel = 0;

			if($needACK === true){
				$pk->identifierACK = $this->identifiersACK[$sessionId]++;
			}

			$this->interface->sendEncapsulated($sessionId, $pk, ($needACK === true ? RakLib::FLAG_NEED_ACK : 0) | ($immediate === true ? RakLib::PRIORITY_IMMEDIATE : RakLib::PRIORITY_NORMAL));
			return $pk->identifierACK;
		}

		return null;
	}

	private function getPacket(string $buffer) : ?DataPacket{
		$pid = ord($buffer[0]);
		if(($data = PacketPool::getPacketById($pid)) === null){
			return null;
		}
		$data->setBuffer($buffer, 1);
		return $data;
	}
}
