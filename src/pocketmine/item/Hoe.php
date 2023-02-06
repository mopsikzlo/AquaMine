<?php

declare(strict_types=1);

namespace pocketmine\item;

use pocketmine\block\Block;
use pocketmine\block\BlockToolType;
use pocketmine\entity\Entity;

class Hoe extends TieredTool{

    public function getBlockToolType() : int{
        return Tool::TYPE_HOE;
    }

    public function onAttackEntity(Entity $victim) : bool{
        return $this->applyDamage(1);
    }

    public function onDestroyBlock(Block $block) : bool{
        if($block->getHardness() > 0){
            return $this->applyDamage(1);
        }
        return false;
    }
}