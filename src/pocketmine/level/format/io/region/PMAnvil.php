<?php

declare(strict_types=1);

namespace pocketmine\level\format\io\region;

use pocketmine\level\format\Chunk;
use pocketmine\level\format\ChunkException;
use pocketmine\level\format\io\ChunkUtils;
use pocketmine\level\format\SubChunk;
use pocketmine\level\LevelException;
use pocketmine\nbt\BigEndianNbtSerializer;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\{
	ByteArrayTag, ByteTag, CompoundTag, IntArrayTag, IntTag, ListTag, LongTag
};
use pocketmine\nbt\TreeRoot;
use pocketmine\Player;
use pocketmine\utils\MainLogger;

/**
 * This format is exactly the same as the PC Anvil format, with the only difference being that the stored data order
 * is XZY instead of YZX for more performance loading and saving worlds.
 */

use pocketmine\utils\Zlib;
use const ZLIB_ENCODING_DEFLATE;

class PMAnvil extends Anvil{

	public const REGION_FILE_EXTENSION = "mcapm";

	public function nbtSerialize(Chunk $chunk) : string{
		$nbt = new CompoundTag();
		$nbt->setInt("xPos", $chunk->getX());
		$nbt->setInt("zPos", $chunk->getZ());

		$nbt->setByte("V", 1);
		$nbt->setLong("LastUpdate", 0); //TODO
		$nbt->setLong("InhabitedTime", 0); //TODO
		$nbt->setByte("TerrainPopulated", $chunk->isPopulated() ? 1 : 0);
		$nbt->setByte("LightPopulated", $chunk->isLightPopulated() ? 1 : 0);

		$sections = new ListTag([], NBT::TAG_Compound);
		foreach($chunk->getSubChunks() as $y => $subChunk){
			if($subChunk->isEmpty()){
				continue;
			}
			$sections->push(CompoundTag::create()
				->setByte("Y", $y)
				->setByteArray("Blocks", $subChunk->getBlockIdArray())
				->setByteArray("Data", $subChunk->getBlockDataArray())
				->setByteArray("SkyLight", $subChunk->getBlockSkyLightArray())
				->setByteArray("BlockLight", $subChunk->getBlockLightArray())
			);
		}
		$nbt->setTag("Sections", $sections);

		$nbt->setByteArray("Biomes", $chunk->getBiomeIdArray());
		$nbt->setIntArray("HeightMap", $chunk->getHeightMapArray());

		$entities = [];

		foreach($chunk->getEntities() as $entity){
			if(!($entity instanceof Player) and !$entity->closed){
				$entity->saveNBT();
				$entities[] = $entity->namedtag;
			}
		}

		$nbt->setTag("Entities", new ListTag($entities));

		$tiles = [];
		foreach($chunk->getTiles() as $tile){
			$tile->saveNBT();
			$tiles[] = $tile->namedtag;
		}

		$nbt->setTag("TileEntities", new ListTag($tiles));

		//TODO: TileTicks

		return Zlib::compress(
			(new BigEndianNbtSerializer())->write(new TreeRoot(
				CompoundTag::create()
					->setTag("Level", $nbt))
			),
			ZLIB_ENCODING_DEFLATE,
			RegionLoader::$COMPRESSION_LEVEL
		);
	}

	public function nbtDeserialize(string $data){
		try{
			$data = @Zlib::decompress($data);
			if($data === false){
				throw new LevelException("Corrupted chunk data");
			}

			$chunk = (new BigEndianNbtSerializer())->read($data)->mustGetCompoundTag();

			if(!$chunk->hasTag("Level", CompoundTag::class)){
				throw new ChunkException("Invalid NBT format");
			}

			$chunk = $chunk->getCompoundTag("Level");

			$subChunks = [];
			if($chunk->hasTag("Sections", ListTag::class)){
				foreach($chunk->getListTag("Sections") as $subChunk){
					if($subChunk instanceof CompoundTag){
						$subChunks[$subChunk->getByte("Y")] = new SubChunk(
							$subChunk->getByteArray("Blocks"),
							$subChunk->getByteArray("Data"),
							$subChunk->getByteArray("SkyLight"),
							$subChunk->getByteArray("BlockLight")
						);
					}
				}
			}

			$result = new Chunk(
				$chunk->getInt("xPos"),
				$chunk->getInt("zPos"),
				$subChunks,
				$chunk->hasTag("Entities", ListTag::class) ? $chunk->getListTag("Entities")->getValue() : [],
				$chunk->hasTag("TileEntities", ListTag::class) ? $chunk->getListTag("TileEntities")->getValue() : [],
				$chunk->hasTag("Biomes", ByteArrayTag::class) ? $chunk->getByteArray("Biomes") : "",
				$chunk->hasTag("HeightMap", IntArrayTag::class) ? $chunk->getIntArray("HeightMap") : []
			);
			$result->setLightPopulated($chunk->getByte("LightPopulated", 0) > 0);
			$result->setPopulated($chunk->getByte("TerrainPopulated", 0) > 0);
			$result->setGenerated(true);
			return $result;
		}catch(\Throwable $e){
			MainLogger::getLogger()->logException($e);
			return null;
		}
	}

	public static function getProviderName() : string{
		return "pmanvil";
	}

	public static function getPcWorldFormatVersion() : int{
		return -1; //Not a PC format, only PocketMine-MP
	}
}