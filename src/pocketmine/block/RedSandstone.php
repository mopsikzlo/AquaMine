<?php

declare(strict_types=1);

namespace pocketmine\block;

class RedSandstone extends Sandstone{
	protected $id = self::RED_SANDSTONE;

	public function getName(){
		static $names = [
			self::NORMAL => "Red Sandstone",
			self::CHISELED => "Chiseled Red Sandstone",
			self::SMOOTH => "Smooth Red Sandstone",
			3 => "",
		];
		return $names[$this->meta & 0x03];
	}
}