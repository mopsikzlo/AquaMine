<?php

namespace pocketmine\level\generator\populator;

use pocketmine\level\generator\populator\VariableAmountPopulator;
use pocketmine\block\Block;
use pocketmine\level\ChunkManager;
use pocketmine\utils\Random;

class SunflowerPlant extends VariableAmountPopulator {

    /** @var ChunkManager */
    private $level;

    public function populate(ChunkManager $level, $chunkX, $chunkZ, Random $random) {
        $this->level = $level;
        $amount = $this->getAmount($random);
        for ($i = 0; $i < $amount; ++$i) {
            $x = $random->nextRange($chunkX * 16, $chunkX * 16 + 15);
            $z = $random->nextRange($chunkZ * 16, $chunkZ * 16 + 15);
            $y = $this->getHighestWorkableBlock($x, $z, $level);

            if ($y !== -1 and $this->canStayAtBlock($x, $y, $z, $level)) {
                $this->level->setBlockIdAt($x, $y, $z, Block::DOUBLE_PLANT);
                $this->level->setBlockDataAt($x, $y, $z, 0);
                $this->level->setBlockIdAt($x, $y + 1, $z, Block::DOUBLE_PLANT);
                $this->level->setBlockDataAt($x, $y + 1, $z, 8);
            }
        }
    }
}
