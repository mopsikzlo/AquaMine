<?php

declare(strict_types=1);

namespace pocketmine\metadata;

use pocketmine\level\Level;

use function strtolower;

class LevelMetadataStore extends MetadataStore{

	public function disambiguate(Metadatable $level, string $metadataKey) : string{
		if(!($level instanceof Level)){
			throw new \InvalidArgumentException("Argument must be a Level instance");
		}

		return strtolower($level->getName()) . ":" . $metadataKey;
	}
}