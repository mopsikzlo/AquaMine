<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\item\Tool;
use pocketmine\level\sound\DoorSound;
use pocketmine\math\AxisAlignedBB;
use pocketmine\Player;

class FenceGate extends Transparent{

	public function getHardness(){
		return 2;
	}

	public function getToolType(){
		return Tool::TYPE_AXE;
	}


	protected function recalculateBoundingBox(){

		if(($this->getDamage() & 0x04) > 0){
			return null;
		}

		$i = ($this->getDamage() & 0x03);
		if($i === 2 or $i === 0){
			return new AxisAlignedBB(
				$this->x,
				$this->y,
				$this->z + 0.375,
				$this->x + 1,
				$this->y + 1.5,
				$this->z + 0.625
			);
		}else{
			return new AxisAlignedBB(
				$this->x + 0.375,
				$this->y,
				$this->z,
				$this->x + 0.625,
				$this->y + 1.5,
				$this->z + 1
			);
		}
	}

	public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null){
		$this->meta = ($player instanceof Player ? ($player->getDirection() - 1) & 0x03 : 0);
		$this->getLevel()->setBlock($block, $this, true, true);

		return true;
	}

	public function getDrops(Item $item){
		return [
			[$this->id, 0, 1],
		];
	}

	public function onActivate(Item $item, Player $player = null){
		$this->meta = (($this->meta ^ 0x04) & ~0x02);

		if($player !== null){
			$this->meta |= (($player->getDirection() - 1) & 0x02);
		}

		$this->getLevel()->setBlock($this, $this, true);
		$this->level->addSound(new DoorSound($this));
		return true;
	}

	public function canBeActivated() : bool{
		return true;
	}
}
