<?php

declare(strict_types=1);

namespace pocketmine\level\light;

class SkyLightUpdate extends LightUpdate{

	public function getLight(int $x, int $y, int $z) : int{
		return $this->subChunkHandler->currentSubChunk->getBlockSkyLight($x & 0x0f, $y & 0x0f, $z & 0x0f);
	}

	public function setLight(int $x, int $y, int $z, int $level){
		$this->subChunkHandler->currentSubChunk->setBlockSkyLight($x & 0x0f, $y & 0x0f, $z & 0x0f, $level);
	}
}