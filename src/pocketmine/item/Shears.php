<?php

declare(strict_types=1);

namespace pocketmine\item;


use pocketmine\block\Block;

class Shears extends Tool{
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::SHEARS, $meta, $count, "Shears");
	}

	public function getMaxDurability() : int{
		return 239;
	}

	public function getBlockToolType(): int {
		return Tool::TYPE_SHEARS;
	}

	public function getBlockToolHarvestLevel() : int{
		return 1;
	}

	protected function getBaseMiningEfficiency() : float{
        return 15;
    }

	public function onDestroyBlock(Block $block) : bool{
		if($block->getHardness() === 0.0 or $block->isCompatibleWithTool($this)){
			return $this->applyDamage(1);
		}
		return false;
	}

	public function isShears() {
		return true;
	}
}