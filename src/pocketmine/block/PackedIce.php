<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\Tool;

class PackedIce extends Solid{

	protected $id = self::PACKED_ICE;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getName(){
		return "Packed Ice";
	}

	public function getFrictionFactor(){
		return 0.98;
	}

	public function getHardness(){
		return 0.5;
	}

	public function getToolType(){
		return Tool::TYPE_PICKAXE;
	}

}