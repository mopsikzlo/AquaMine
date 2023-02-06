<?php

declare(strict_types=1);

namespace pocketmine\block;


class Wood2 extends Wood{

	public const ACACIA = 0;
	public const DARK_OAK = 1;

	protected $id = self::WOOD2;

	public function getName(){
		static $names = [
			0 => "Acacia Wood",
			1 => "Dark Oak Wood",
			2 => "Unknown",
			3 => "Unknown"
		];
		return $names[$this->meta & 0x03];
	}
}
