<?php

declare(strict_types=1);

namespace pocketmine\resourcepacks;

class ResourcePackInfoEntry{
	/** @var string */
	protected $packId; //UUID
	/** @var string */
	protected $version;
	/** @var int  */
	protected $packSize;

	public function __construct(string $packId, string $version, int $packSize = 0){
		$this->packId = $packId;
		$this->version = $version;
		$this->packSize = $packSize;
	}

	public function getPackId() : string{
		return $this->packId;
	}

	public function getVersion() : string{
		return $this->version;
	}

	public function getPackSize() : int{
		return $this->packSize;
	}
}