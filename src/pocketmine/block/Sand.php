<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\Tool;

class Sand extends Fallable{

	protected $id = self::SAND;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getHardness(){
		return 0.5;
	}

	public function getToolType(){
		return Tool::TYPE_SHOVEL;
	}

	public function getName(){
		if($this->meta === 0x01){
			return "Red Sand";
		}

		return "Sand";
	}

}