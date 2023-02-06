<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\palette;

use function array_flip;

final class ActorMapping{

	private function __construct(){
		//NOOP
	}

	/** @var int[] */
	private static $stringToLegacyIdMap = [];
	/** @var string[] */
	private static $legacyToStringIdMap = [];

	/** @var string */
	private static $encodedActorIdentifiers;

	public static function init() : void{
		self::$stringToLegacyIdMap = json_decode(file_get_contents(\pocketmine\PATH . "src/pocketmine/resources/bedrock/actor_id_map.json"), true);
		self::$stringToLegacyIdMap[":"] = 1; //empty id

		self::$legacyToStringIdMap = array_flip(self::$stringToLegacyIdMap);
		self::$stringToLegacyIdMap[""] = 1; //another empty id

		self::$encodedActorIdentifiers = file_get_contents(\pocketmine\PATH . "src/pocketmine/resources/bedrock/actor_identifiers.nbt");
	}

	/**
	 * @param int $entityId
	 *
	 * @return string
	 */
	public static function getStringIdFromLegacyId(int $entityId) : string{
		return self::$legacyToStringIdMap[$entityId] ?? ":";
	}

	/**
	 * @param string $stringId
	 *
	 * @return int
	 */
	public static function getLegacyIdFromStringId(string $stringId) : int{
		return self::$stringToLegacyIdMap[$stringId] ?? -1;
	}

	/**
	 * @return string
	 */
	public static function getEncodedActorIdentifiers() : string{
		return self::$encodedActorIdentifiers;
	}
}