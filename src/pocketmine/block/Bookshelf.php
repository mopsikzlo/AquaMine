<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\item\Tool;

class Bookshelf extends Solid{

	protected $id = self::BOOKSHELF;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getName(){
		return "Bookshelf";
	}

	public function getHardness(){
		return 1.5;
	}

	public function getToolType(){
		return Tool::TYPE_AXE;
	}

	public function getDrops(Item $item){
		return [
			[Item::BOOK, 0, 3]
		];
	}

}