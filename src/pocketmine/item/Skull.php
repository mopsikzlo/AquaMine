<?php

declare(strict_types=1);

namespace pocketmine\item;

use pocketmine\block\Block;

class Skull extends Item{

	public const SKULL_SKELETON = 0;
	public const SKULL_WITHER_SKELETON = 1;
	public const SKULL_ZOMBIE = 2;
	public const SKULL_HUMAN = 3;
	public const SKULL_CREEPER = 4;
	public const SKULL_DRAGON = 5;

	public function __construct($meta = 0, $count = 1){
		$this->block = Block::get(Block::SKULL_BLOCK);
		parent::__construct(self::SKULL, $meta, $count, "Mob Head");
	}
}