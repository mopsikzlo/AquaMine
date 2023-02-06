<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol\types;

use pocketmine\utils\UUID;

class PlayerListEntry{

	/** @var UUID */
	public $uuid;
	/** @var int */
	public $entityUniqueId;
	/** @var string */
	public $username;
	/** @var Skin */
	public $skin;

	public static function createRemovalEntry(UUID $uuid) : PlayerListEntry{
		$entry = new PlayerListEntry();
		$entry->uuid = $uuid;

		return $entry;
	}

	public static function createAdditionEntry(UUID $uuid, int $entityUniqueId, string $username, Skin $skin) : PlayerListEntry{
		$entry = new PlayerListEntry();
		$entry->uuid = $uuid;
		$entry->entityUniqueId = $entityUniqueId;
		$entry->username = $username;
		$entry->skin = $skin;

		return $entry;
	}
}
