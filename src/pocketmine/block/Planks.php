<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\Tool;

class Planks extends Solid{
	public const OAK = 0;
	public const SPRUCE = 1;
	public const BIRCH = 2;
	public const JUNGLE = 3;
	public const ACACIA = 4;
	public const DARK_OAK = 5;

	protected $id = self::WOODEN_PLANKS;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getHardness(){
		return 2;
	}

	public function getToolType(){
		return Tool::TYPE_AXE;
	}

	public function getName(){
		static $names = [
			self::OAK => "Oak Wood Planks",
			self::SPRUCE => "Spruce Wood Planks",
			self::BIRCH => "Birch Wood Planks",
			self::JUNGLE => "Jungle Wood Planks",
			self::ACACIA => "Acacia Wood Planks",
			self::DARK_OAK => "Dark Oak Wood Planks",
			"",
			""
		];
		return $names[$this->meta & 0x07];
	}

}
