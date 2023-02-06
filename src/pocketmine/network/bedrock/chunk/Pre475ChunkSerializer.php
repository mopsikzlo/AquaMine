<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\chunk;

use Closure;
use pocketmine\level\format\Chunk;
use pocketmine\level\format\SubChunkInterface;
use pocketmine\tile\Spawnable;
use pocketmine\utils\BinaryStream;
use pocketmine\utils\Utils;
use pocketmine\world\format\io\SubChunkConverter;
use function count;

final class Pre475ChunkSerializer{

	private function __construct(){
		//NOOP
	}

	public static function serialize(Chunk $chunk, Closure $legacyToRuntime, ?string $tiles = null) : string{
		$buffer = "";

		for($y = 0, $subChunkCount = $chunk->getSubChunkSendCount(); $y < $subChunkCount; ++$y){
			$subChunk = $chunk->getSubChunk($y);
			$buffer .= self::serializeSubChunk($subChunk, $legacyToRuntime);
		}

		$buffer .= $chunk->getBiomeIdArray();
		$buffer .= "\x00"; //border block array count
		//Border block entry format: 1 byte (4 bits X, 4 bits Z). These are however useless since they crash the regular client.

		if($tiles !== null){
			$buffer .= $tiles;
		}else{
			$buffer .= self::serializeTiles($chunk);
		}
		return $buffer;
	}

	public static function serializeSubChunk(SubChunkInterface $subChunk, Closure $legacyToRuntime) : string{
		Utils::validateCallableSignature(function(int $blockId, int $meta) : int{}, $legacyToRuntime);
		$result = new BinaryStream();
		$result->putByte(8); // storage version

		$result->putByte(1); // layer count

		$blocks = SubChunkConverter::convertSubChunkXZY($subChunk->getBlockIdArray(), $subChunk->getBlockDataArray());

		// 1 is network format (palette out of runtimeIDs), 0 is storage format (palette out of NBT tags)
		$result->putByte(($blocks->getBitsPerBlock() << 1) | 1);
		$result->put($blocks->getWordArray()); // LInt array
		$palette = $blocks->getPalette();

		//these LSHIFT by 1 uvarints are optimizations: the client expects zigzag varints here
		//but since we know they are always unsigned, we can avoid the extra fcall overhead of
		//zigzag and just shift directly.
		$result->putUnsignedVarInt(count($palette) << 1);
		foreach($palette as $fullBlock){
			$runtimeId = $legacyToRuntime($fullBlock >> 4, $fullBlock & 0x0f);
			$result->putUnsignedVarInt($runtimeId << 1);
		}
		return $result->buffer;
	}

	public static function serializeTiles(Chunk $chunk) : string{
		$buffer = "";
		foreach($chunk->getTiles() as $tile){
			if($tile instanceof Spawnable){
				$buffer .= $tile->getSerializedSpawnCompound(true);
			}
		}
		return $buffer;
	}
}
