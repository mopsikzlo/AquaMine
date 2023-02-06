<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\palette;

use InvalidArgumentException;
use InvalidStateException;
use pocketmine\network\mcpe\NetworkBinaryStream;
use UnexpectedValueException;
use function count;
use function file_get_contents;
use function json_decode;

final class ItemPalette{

	private function __construct(){
		//NOOP
	}

	/** @var int[] */
	private static $stringToRuntimeIdMap = [];
	/** @var string[] */
	private static $runtimeToStringIdMap = [];

	/** @var int[] */
	private static $simpleLegacyToRuntimeIdMap = [];
	/** @var int[] */
	private static $simpleRuntimeToLegacyIdMap = [];

	/** @var int[][] */
	private static $complexLegacyToRuntimeIdMap = []; // array[internalID][metadata] = runtimeID
	/** @var int[][] */
	private static $complexRuntimeToLegacyIdMap = []; // array[runtimeID] = [internalID, metadata]

	/** @var string */
	private static $encodedPalette;

	public static function init() : void{
		/** @var int[] $itemPalette */
		$itemMap = json_decode(file_get_contents(\pocketmine\PATH . "src/pocketmine/resources/bedrock/r16_to_current_item_map.json"), true);

		/** @var int[] $itemPalette */
		$stringToIntMap = json_decode(file_get_contents(\pocketmine\PATH . "src/pocketmine/resources/bedrock/item_id_map.json"), true);

		$simpleMappings = [];
		foreach($itemMap["simple"] as $oldId => $newId){
            if(!isset($stringToIntMap[$oldId])){
                //new item without a fixed legacy ID - we can't handle this right now
                continue;
            }
			$simpleMappings[$newId] = $stringToIntMap[$oldId];
		}
		foreach($stringToIntMap as $stringId => $intId){
			if(isset($simpleMappings[$stringId])){
				throw new InvalidStateException("Old ID $stringId collides with new ID");
			}
			$simpleMappings[$stringId] = $intId;
		}

		$complexMappings = [];
		foreach($itemMap["complex"] as $oldId => $map){
			foreach($map as $meta => $newId){
				if(isset($stringToIntMap[$oldId])){
					$complexMappings[$newId] = [$stringToIntMap[$oldId], (int) $meta];
				}
			}
		}

		$itemList = json_decode(file_get_contents(\pocketmine\PATH . "src/pocketmine/resources/bedrock/required_item_list.json"), true);
		foreach($itemList as $stringId => $entry){
			$runtimeId = $entry["runtime_id"];

			self::$stringToRuntimeIdMap[$stringId] = $runtimeId;
			self::$runtimeToStringIdMap[$runtimeId] = $stringId;

			if(isset($complexMappings[$stringId])){
				[$id, $meta] = $complexMappings[$stringId];
				self::$complexLegacyToRuntimeIdMap[$id][$meta] = $runtimeId;
				self::$complexRuntimeToLegacyIdMap[$runtimeId] = [$id, $meta];
			}elseif(isset($simpleMappings[$stringId])){
				self::$simpleLegacyToRuntimeIdMap[$simpleMappings[$stringId]] = $runtimeId;
				self::$simpleRuntimeToLegacyIdMap[$runtimeId] = $simpleMappings[$stringId];
			}else{
                //not all items have a legacy mapping - for now, we only support the ones that do
                continue;
            }
		}

		$stream = new NetworkBinaryStream();
		$stream->putUnsignedVarInt(count($itemList));
		foreach($itemList as $stringId => $entry){
			$stream->putString($stringId);
			$stream->putLShort($entry["runtime_id"]);
			$stream->putBool($entry["component_based"]);
		}
		self::$encodedPalette = $stream->buffer;
	}

	/**
	 * @param int $runtimeId
	 *
	 * @return string
	 */
	public static function getStringFromRuntimeId(int $runtimeId) : string{
		return self::$runtimeToStringIdMap[$runtimeId] ?? "minecraft:unknown";
	}

	/**
	 * @param string $stringId
	 *
	 * @return int
	 */
	public static function getRuntimeFromStringId(string $stringId) : int{
		return self::$stringToRuntimeIdMap[$stringId] ?? -1;
	}

	/**
	 * @param int $id
	 * @param int $meta
	 *
	 * @return int[]
	 */
	public static function getRuntimeFromLegacyId(int $id, int $meta = 0) : array{
		if(isset(self::$complexLegacyToRuntimeIdMap[$id][$meta])){
			return [self::$complexLegacyToRuntimeIdMap[$id][$meta], 0];
		}
		if(isset(self::$simpleLegacyToRuntimeIdMap[$id])){
			return [self::$simpleLegacyToRuntimeIdMap[$id], $meta];
		}

		throw new InvalidArgumentException("Unmapped ID/metadata combination $id:$meta");
	}

	/**
	 * @param int $runtimeId
	 * @param int $runtimeMeta
	 * @param false $isComplex
	 *
	 * @return int[]
	 */
	public static function getLegacyFromRuntimeId(int $runtimeId, int $runtimeMeta, &$isComplex = false) : array{
		if(isset(self::$complexRuntimeToLegacyIdMap[$runtimeId])){
			if($runtimeMeta !== 0){
				throw new UnexpectedValueException("Unexpected non-zero network meta on complex item mapping");
			}

			$isComplex = true;
			return self::$complexRuntimeToLegacyIdMap[$runtimeId];
		}
		if(isset(self::$simpleRuntimeToLegacyIdMap[$runtimeId])){
			return [self::$simpleRuntimeToLegacyIdMap[$runtimeId], $runtimeMeta];
		}
		throw new InvalidArgumentException("Unmapped network ID/metadata combination $runtimeId:$runtimeMeta");
	}

	public static function getLegacyFromRuntimeIdWildcard(int $runtimeId, int $runtimeMeta) : array{
		if($runtimeMeta !== 0x7fff){
			return self::getLegacyFromRuntimeId($runtimeId, $runtimeMeta);
		}

		$isComplex = false;
		[$id, $meta] = self::getLegacyFromRuntimeId($runtimeId, 0, $isComplex);

		if($isComplex){
			return [$id, $meta];
		}else{
			return [$id, -1];
		}
	}

	/**
	 * @return string
	 */
	public static function getEncodedPalette() : string{
		return self::$encodedPalette;
	}
}