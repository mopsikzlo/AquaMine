<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\chunk;

use pocketmine\level\format\Chunk;
use pocketmine\tile\Spawnable;
use pocketmine\utils\BinaryStream;
use function pack;

final class MCPEChunkSerializer{

	private function __construct(){
		//NOOP
	}

	public static function serialize(Chunk $chunk) : string{
		$result = new BinaryStream();
		$subChunkCount = $chunk->getSubChunkSendCount();
		$result->putByte($subChunkCount);
		for($y = 0; $y < $subChunkCount; ++$y){
			$subChunk = $chunk->getSubChunk($y);

			$result->putByte(0); //storage version
			$result->put($subChunk->getBlockIdArray());
			$result->put($subChunk->getBlockDataArray());
			/*$result->put($subChunk->getBlockSkyLightArray());
			$result->put($subChunk->getBlockLightArray());*/

			// HACK! No shadows for 1.1
			$result->put(\pocketmine\level\format\FIFTEEN_NIBBLE_ARRAY); // sky light
			$result->put(\pocketmine\level\format\ZERO_NIBBLE_ARRAY); // block light
		}
		$result->put(pack("v*", ...$chunk->getHeightMapArray()));
		$result->put($chunk->getBiomeIdArray());
		$result->putByte(0); //border block array count
		//Border block entry format: 1 byte (4 bits X, 4 bits Z). These are however useless since they crash the regular client.

		$result->putVarInt(count($chunk->getBlockExtraDataArray())); //WHY, Mojang, WHY
		foreach($chunk->getBlockExtraDataArray() as $key => $value){
			$result->putVarInt($key);
			$result->putLShort($value);
		}

		foreach($chunk->getTiles() as $tile){
			if($tile instanceof Spawnable){
				$result->put($tile->getSerializedSpawnCompound(false));
			}
		}

		return $result->buffer;
	}
}