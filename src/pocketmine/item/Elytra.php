<?php

declare(strict_types=1);

namespace pocketmine\item;

use pocketmine\math\Vector3;
use pocketmine\Player;

class Elytra extends Durable{
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::ELYTRA, $meta, $count, "Elytra");
	}

	public function canBeUsedOnAir() : bool{
		return true;
	}

	public function onClickAir(Player $player, Vector3 $directionVector) : bool{
		if($player->getInventory()->getChestplate()->getId() === Item::AIR){
			$player->getInventory()->setChestplate($this);
			$player->getInventory()->setItemInHand(Item::get(Item::AIR));
		}
		return true;
	}

	public function getMaxDurability(){
		return 431;
	}
}
