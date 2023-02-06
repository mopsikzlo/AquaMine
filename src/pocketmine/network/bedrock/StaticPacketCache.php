<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock;

use pocketmine\network\bedrock\adapter\ProtocolAdapterFactory;
use pocketmine\network\bedrock\palette\ActorMapping;
use pocketmine\network\bedrock\protocol\AvailableActorIdentifiersPacket;
use pocketmine\network\bedrock\protocol\BiomeDefinitionListPacket;
use pocketmine\network\bedrock\protocol\ProtocolInfo;
use function file_get_contents;

final class StaticPacketCache{
	/** @var string[] */
	private static $biomeDefs = [];
	/** @var string[] */
	private static $availableActorIdentifiers = [];

	public static function init() : void{
		$biomeDefs = new BiomeDefinitionListPacket();
		$biomeDefs->namedtag = file_get_contents(\pocketmine\PATH . "src/pocketmine/resources/bedrock/biome_definitions.nbt");

		$actorIdentifiers = new AvailableActorIdentifiersPacket();
		$actorIdentifiers->namedtag = ActorMapping::getEncodedActorIdentifiers();

		$protocols = [ProtocolInfo::CURRENT_PROTOCOL];
		foreach(ProtocolAdapterFactory::getAll() as $adapter){
			$protocols[] = $adapter->getProtocolVersion();
		}

		foreach($protocols as $protocol){
			$adapter = ProtocolAdapterFactory::get($protocol);

			$stream = new BedrockPacketBatch();
			if($adapter === null){
				$stream->putPacket($biomeDefs);
			}else{
				$stream->putPacket($adapter->processServerToClient($biomeDefs));
			}

			self::$biomeDefs[$protocol] = NetworkCompression::compress($stream->buffer);

			$stream->reset();
			if($adapter === null){
				$stream->putPacket($actorIdentifiers);
			}else{
				$stream->putPacket($adapter->processServerToClient($actorIdentifiers));
			}

			self::$availableActorIdentifiers[$protocol] = NetworkCompression::compress($stream->buffer);
		}
	}

	/**
	 * @param int $protocol
	 *
	 * @return string
	 */
	public static function getBiomeDefs(int $protocol) : string{
		return self::$biomeDefs[$protocol];
	}

	/**
	 * @param int $protocol
	 *
	 * @return string
	 */
	public static function getAvailableActorIdentifiers(int $protocol) : string{
		return self::$availableActorIdentifiers[$protocol];
	}
}