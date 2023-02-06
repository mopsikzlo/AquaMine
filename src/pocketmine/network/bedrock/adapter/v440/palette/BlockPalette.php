<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\adapter\v440\palette;

use pocketmine\block\BlockIds;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\TreeRoot;
use pocketmine\network\bedrock\palette\R12ToCurrentBlockMapEntry;
use pocketmine\network\mcpe\NetworkBinaryStream;
use pocketmine\network\mcpe\NetworkNbtSerializer;
use function array_map;
use function count;
use function file_get_contents;
use function json_decode;

final class BlockPalette{

	private function __construct(){
		//NOOP
	}

	/** @var int[] */
	private static $runtimeToLegacyIdMap = [];
	/** @var int[] */
	private static $legacyToRuntimeIdMap = [];

	/** @var string */
	private static $encodedPalette;

	public static function init() : void{
		$nbt = new NetworkNbtSerializer();

		$rawData = file_get_contents(\pocketmine\PATH . "src/pocketmine/resources/bedrock/v440/canonical_block_states.nbt");

		$blockStates = array_map(function(TreeRoot $root) : CompoundTag{
			return $root->mustGetCompoundTag();
		}, $nbt->readMultiple($rawData));

		$legacyIdMap = json_decode(file_get_contents(\pocketmine\PATH . "src/pocketmine/resources/bedrock/v465/block_id_map.json"), true);

		/** @var R12ToCurrentBlockMapEntry[] $legacyStateMap */
		$legacyStateMap = [];
		$legacyStateMapReader = new NetworkBinaryStream(file_get_contents(\pocketmine\PATH . "src/pocketmine/resources/bedrock/v448/r12_to_current_block_map.bin"));

		while(!$legacyStateMapReader->feof()){
			$id = $legacyStateMapReader->getString();
			$meta = $legacyStateMapReader->getLShort();

			$state = $legacyStateMapReader->getNbtCompoundRoot();
			$legacyStateMap[] = new R12ToCurrentBlockMapEntry($id, $meta, $state);
		}

		/**
		 * @var int[][] $idToStatesMap string id -> int[] list of candidate state indices
		 */
		$idToStatesMap = [];
		foreach($blockStates as $runtimeId => $state){
			$idToStatesMap[$state->getString("name")][] = $runtimeId;
		}

		foreach($legacyStateMap as $pair){
			$id = $legacyIdMap[$pair->getId()] ?? null;
			if($id === null){
				throw new \RuntimeException("No legacy ID matches " . $pair->getId());
			}
			$data = $pair->getMeta();

			$mappedState = $pair->getBlockState();
			$mappedName = $mappedState->getString("name");

			if(!isset($idToStatesMap[$mappedName])){
				throw new \RuntimeException("Mapped new state does not appear in network table");
			}

			foreach($idToStatesMap[$mappedName] as $k){
				$networkState = $blockStates[$k];
				if($mappedState->equals($networkState)){
					self::registerMapping($k, $id, $data);
					continue 2;
				}
			}
			throw new \RuntimeException("Mapped new state does not appear in network table");
		}

		$stream = new NetworkBinaryStream();
		$stream->putUnsignedVarInt(count($blockStates));

		foreach($blockStates as $state){
			$stream->putString($state->getString("name"));

			$stream->put($nbt->write(new TreeRoot($state->getCompoundTag("states"))));
		}
		self::$encodedPalette = $stream->buffer;
	}

	private static function registerMapping(int $runtimeId, int $legacyId, int $legacyMeta) : void{
		self::$legacyToRuntimeIdMap[($legacyId << 5) | $legacyMeta] = $runtimeId;
		self::$runtimeToLegacyIdMap[$runtimeId] = ($legacyId << 5) | $legacyMeta;
	}

	public static function lazyInit() : void{
		if(self::$encodedPalette === null){
			self::init();
		}
	}

	/**
	 * @param int $id
	 * @param int $meta
	 *
	 * @return int
	 */
	public static function getRuntimeFromLegacyId(int $id, int $meta = 0) : int{
		/*
		* try id+meta first
		* if not found, try id+0 (strip meta)
		* if still not found, return update! block
		*/
		return self::$legacyToRuntimeIdMap[($id << 5) | $meta] ?? self::$legacyToRuntimeIdMap[$id << 5] ?? self::$legacyToRuntimeIdMap[BlockIds::INFO_UPDATE << 5];
	}

	/**
	 * @param int $runtimeId
	 * @param &$id = 0
	 * @param &$meta = 0
	 */
	public static function getLegacyFromRuntimeId(int $runtimeId, &$id = 0, &$meta = 0) : void{
		if(isset(self::$runtimeToLegacyIdMap[$runtimeId])){
			$v = self::$runtimeToLegacyIdMap[$runtimeId];
			$id = $v >> 5;
			$meta = $v & 0xf;
		}
	}

	/**
	 * @return string
	 */
	public static function getEncodedPalette() : string{
		return self::$encodedPalette;
	}
}