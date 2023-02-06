<?php

declare(strict_types=1);

namespace pocketmine\metadata;

use pocketmine\IPlayer;

use function strtolower;

class PlayerMetadataStore extends MetadataStore{

	public function disambiguate(Metadatable $player, string $metadataKey) : string{
		if(!($player instanceof IPlayer)){
			throw new \InvalidArgumentException("Argument must be an IPlayer instance");
		}

		return strtolower($player->getName()) . ":" . $metadataKey;
	}
}
