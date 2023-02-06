<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\adapter\v407\palette;

use pocketmine\item\ItemIds;
use pocketmine\network\mcpe\NetworkBinaryStream;
use function count;
use function file_get_contents;
use function json_decode;

class ItemPalette{

	/** @var int[] */
	protected static $stringToNumericIdMap = [];
	/** @var string[] */
	protected static $numericToStringIdMap = [];

	/** @var string */
	protected static $encodedPalette;

	public static function init() : void{
		/** @var int[] $itemPalette */
		$itemPalette = json_decode(file_get_contents(\pocketmine\PATH . "src/pocketmine/resources/bedrock/v407/item_id_map.json"), true);

		$stream = new NetworkBinaryStream();
		$stream->putUnsignedVarInt(count($itemPalette));

		foreach($itemPalette as $name => $id){
			self::$stringToNumericIdMap[$name] = $id;
			self::$numericToStringIdMap[$id] = $name;

			$stream->putString($name);
			$stream->putLShort($id);
		}

		self::$encodedPalette = $stream->getBuffer();
	}

	public static function lazyInit() : void{
		if(self::$encodedPalette === null){
			self::init();
		}
	}

	/**
	 * @param int $numberId
	 *
	 * @return string
	 */
	public static function getStringFromNumericId(int $numberId) : string{
		return self::$numericToStringIdMap[$numberId] ?? "minecraft:unknown";
	}

	/**
	 * @param string $stringId
	 *
	 * @return int
	 */
	public static function getNumericFromStringId(string $stringId) : int{
		return self::$stringToNumericIdMap[$stringId] ?? ItemIds::AIR;
	}

	/**
	 * @return string
	 */
	public static function getEncodedPalette() : string{
		return self::$encodedPalette;
	}
}