<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\Item;


/**
 * Air block
 */
class Air extends Transparent{

	protected $id = self::AIR;
	protected $meta = 0;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getName(){
		return "Air";
	}

	public function canPassThrough(){
		return true;
	}

	public function isBreakable(Item $item){
		return false;
	}

	public function canBeFlowedInto(){
		return true;
	}

	public function canBeReplaced(){
		return true;
	}

	public function canBePlaced(){
		return false;
	}

	public function isSolid(){
		return false;
	}

	public function getBoundingBox(){
		return null;
	}

	public function getCollisionBoxes() : array{
		return [];
	}

	public function getHardness(){
		return -1;
	}

	public function getBlastResistance() : float{
		return 0;
	}

}