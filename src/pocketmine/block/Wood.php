<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\item\Tool;
use pocketmine\Player;

class Wood extends Solid{
	public const OAK = 0;
	public const SPRUCE = 1;
	public const BIRCH = 2;
	public const JUNGLE = 3;

	protected $id = self::WOOD;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getHardness(){
		return 2;
	}

	public function getName(){
		static $names = [
			self::OAK => "Oak Wood",
			self::SPRUCE => "Spruce Wood",
			self::BIRCH => "Birch Wood",
			self::JUNGLE => "Jungle Wood",
		];
		return $names[$this->meta & 0x03];
	}

	public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null){
		$faces = [
			0 => 0,
			1 => 0,
			2 => 0b1000,
			3 => 0b1000,
			4 => 0b0100,
			5 => 0b0100,
		];

		$this->meta = ($this->meta & 0x03) | $faces[$face];
		$this->getLevel()->setBlock($block, $this, true, true);

		return true;
	}

	public function getDrops(Item $item){
		return [
			[$this->id, $this->meta & 0x03, 1],
		];
	}

	public function getToolType(){
		return Tool::TYPE_AXE;
	}
}