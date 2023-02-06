<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\Item;

use pocketmine\item\Shears;
use function mt_rand;

class Leaves2 extends Leaves{

	protected $id = self::LEAVES2;
	protected $woodType = self::WOOD2;

	public function getName(){
		static $names = [
			self::ACACIA => "Acacia Leaves",
			self::DARK_OAK => "Dark Oak Leaves",
		];
		return $names[$this->meta & 0x01];
	}

	public function getDrops(Item $item){
		$drops = [];
		if($item instanceof Shears){
			$drops[] = [$this->id, $this->meta & 0x01, 1];
		}else{
			if(mt_rand(1, 20) === 1){ //Saplings
				$drops[] = [Item::SAPLING, ($this->meta & 0x01) + 4, 1];
			}
		}

		return $drops;
	}
}