<?php

namespace pocketmine\level\generator\populator;

use pocketmine\level\ChunkManager;
use pocketmine\utils\Random;

class IceCover extends VariableAmountPopulator {
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
                $rnd = mt_rand(1, 2);
                if ($rnd == 1) {
                    $this->level->setBlockIdAt($x, $y - 1, $z, 174);
                    $this->level->setBlockDataAt($x, $y - 1, $z, 0);
                } else {
                    $this->level->setBlockIdAt($x, $y - 1, $z, 80);
                    $this->level->setBlockDataAt($x, $y - 1, $z, 0);
                }
            }
        }
    }
}
