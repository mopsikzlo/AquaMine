<?php

declare(strict_types=1);

namespace pocketmine\block;

class Purpur extends Quartz{

	protected $id = self::PURPUR_BLOCK;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getName(){
		static $names = [
			self::QUARTZ_NORMAL => "Purpur Block",
			self::QUARTZ_CHISELED => "Chiseled Purpur", //wtf?
			self::QUARTZ_PILLAR => "Purpur Pillar"
		];

		return $names[$this->meta & 0x03] ?? "Unknown";
	}

	public function getHardness(){
		return 1.5;
	}

	public function getBlastResistance() : float{
		return 30;
	}
}