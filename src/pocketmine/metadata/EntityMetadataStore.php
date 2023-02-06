<?php

declare(strict_types=1);

namespace pocketmine\metadata;

use pocketmine\entity\Entity;

class EntityMetadataStore extends MetadataStore{

	public function disambiguate(Metadatable $entity, string $metadataKey) : string{
		if(!($entity instanceof Entity)){
			throw new \InvalidArgumentException("Argument must be an Entity instance");
		}

		return $entity->getId() . ":" . $metadataKey;
	}
}