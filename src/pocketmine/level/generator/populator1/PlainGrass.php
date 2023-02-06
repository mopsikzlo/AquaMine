<?php

namespace pocketmine\level\generator\populator;

use pocketmine\block\Block;
use pocketmine\level\ChunkManager;
use pocketmine\utils\Random;

class PlainGrass extends VariableAmountPopulator {

    /** @var ChunkManager */
    private $level;

    public function populate(ChunkManager $level, $chunkX, $chunkZ, Random $random) {
        $this->level = $level;
        $amount = $random->nextRange(0, $this->randomAmount + 1) + $this->baseAmount;
        for ($i = 0; $i < $amount; ++$i) {
            $x = $random->nextRange($chunkX * 16, $chunkX * 16 + 15);
            $z = $random->nextRange($chunkZ * 16, $chunkZ * 16 + 15);
            $y = $this->getHighestWorkableBlock($x, $z, $level);

            if ($y !== -1 and $this->canStayAtBlock($x, $y, $z, $level)) {
                if (mt_rand(0, 7) == 0) {
                    $this->level->setBlockIdAt($x, $y, $z, Block::TALL_GRASS);
                    $this->level->setBlockDataAt($x, $y, $z, 2);
                } else {
                    $this->level->setBlockIdAt($x, $y, $z, Block::TALL_GRASS);
                    $this->level->setBlockDataAt($x, $y, $z, 1);
                }
            }
        }
    }
}
