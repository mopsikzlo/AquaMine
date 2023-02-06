<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\entity\Entity;
use pocketmine\entity\EntityDataHelper;
use pocketmine\item\Durable;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\FlintSteel;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\Player;
use function cos;
use function lcg_value;
use function sin;
use const M_PI;

class TNT extends Solid{

	protected $id = self::TNT;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getName(){
		return "TNT";
	}

	public function getHardness(){
		return 0;
	}

	public function onActivate(Item $item, Player $player = null){
		if($item instanceof FlintSteel or $item->hasEnchantment(Enchantment::FIRE_ASPECT)){
			if($item instanceof Durable){
				$item->applyDamage(1);
			}
			$this->ignite();
			return true;
		}

		return false;
	}

	public function canBeActivated() : bool{
		return true;
	}

	public function ignite(int $fuse = 80){
		$this->getLevel()->setBlock($this, new Air(), true);

		$mot = (2.0 * lcg_value() - 1.0) * 2 * M_PI;
		$nbt = EntityDataHelper::createBaseNBT($this->add(0.5, 0, 0.5), new Vector3(-sin($mot) * 0.2, 0.2, -cos($mot) * 0.2));
		$nbt->setByte("Fuse", $fuse);
		$tnt = Entity::createEntity("PrimedTNT", $this->getLevel(), $nbt);

		$tnt->spawnToAll();
	}
}