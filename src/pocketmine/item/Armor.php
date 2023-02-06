<?php

declare(strict_types=1);


namespace pocketmine\item;

use pocketmine\item\enchantment\Enchantment;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\IntTag;
use pocketmine\Player;
use pocketmine\utils\Color;

abstract class Armor extends Durable{

	public function getMaxStackSize(){
		return 1;
	}

	public function setCustomColor(Color $color) : self{
		$this->getNamedTag()->setInt("customColor", $color->toRGB());
		return $this;
	}

	public function getCustomColor() : ?Color{
		$tag = $this->getNamedTag();
		if($tag->hasTag("customColor", IntTag::class)){
			return Color::fromRGB($tag->getInt("customColor"));
		}
		return null;
	}

	public function clearCustomColor(){
		$this->getNamedTag()->removeTag("customColor");
	}

	public function canBeUsedOnAir() : bool{
		return true;
	}

	public function onClickAir(Player $player, Vector3 $directionVector) : bool{
		$slot = ($this->id - 298) % 4;
		if($player->getInventory()->getArmorItem($slot)->getId() === Item::AIR){
			$player->getInventory()->setArmorItem($slot, $this);
			$player->getInventory()->setItemInHand(Item::get(Item::AIR));
		}

		return true;
	}

	protected function getUnbreakingDamageReduction(int $amount) : int{
		if(($unbreaking = $this->getEnchantment(Enchantment::UNBREAKING)) !== null){
			$negated = 0;

			$chance = 1 / ($unbreaking->getLevel() + 1);
			for($i = 0; $i < $amount; ++$i){
				if(mt_rand(1, 100) > 60 and lcg_value() > $chance){ //unbreaking only applies to armor 40% of the time at best
					$negated++;
				}
			}

			return $negated;
		}

		return 0;
	}
}