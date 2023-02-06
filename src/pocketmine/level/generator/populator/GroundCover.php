<?php

namespace pocketmine\level\generator\populator;

use pocketmine\block\Block;
use pocketmine\block\Liquid;
use pocketmine\level\loadchunk\ChunkManager;
use pocketmine\level\generator\biome\Biome;
use pocketmine\level\Level;
use pocketmine\level\SimpleChunkManager;
use pocketmine\utils\Random;

class GroundCover extends Populator {

	/**
	 * @param ChunkManager $level
	 * @param              $chunkX
	 * @param              $chunkZ
	 * @param Random       $random
	 *
	 * @return mixed|void
	 */
	public function populate(ChunkManager $level, $chunkX, $chunkZ, Random $random){
		$chunk = $level->getChunk($chunkX, $chunkZ);
		if($level instanceof Level or $level instanceof SimpleChunkManager){
			$waterHeight = $level->getWaterHeight();
		}else $waterHeight = 0;
		for($x = 0; $x < 16; ++$x){
			for($z = 0; $z < 16; ++$z){
				$biome = Biome::getBiome($chunk->getBiomeId($x, $z));
				$cover = $biome->getGroundCover();
				if(count($cover) > 0){
					$diffY = 0;
					if(!$cover[0]->isSolid()){
						$diffY = 1;
					}

					$column = $chunk->getBlockIdColumn($x, $z);
					$startY = 127;
					for(; $startY > 0; --$startY){
						if($column[$startY] !== "\x00" and !Block::get(ord($column[$startY]))->isTransparent()){
							break;
						}
					}
					$startY = min(127, $startY + $diffY);
					$endY = $startY - count($cover);
					for($y = $startY; $y > $endY and $y >= 0; --$y){
						$b = $cover[$startY - $y];
						if($column[$y] === "\x00" and $b->isSolid()){
							break;
						}
						if($b->canBeFlowedInto() and Block::get(ord($column[$y])) instanceof Liquid){
							continue;
						}
						if($y <= $waterHeight and $b->getId() == Block::GRASS and $chunk->getBlockId($x, $y + 1, $z) == Block::STILL_WATER){
							$b = Block::get(Block::DIRT);
						}
						if($b->getDamage() === 0){
							$chunk->setBlockId($x, $y, $z, $b->getId());
						}else{
							$chunk->setBlock($x, $y, $z, $b->getId(), $b->getDamage());
						}
					}
				}
			}
		}
	}
}