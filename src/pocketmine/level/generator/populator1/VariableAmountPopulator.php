<?php

namespace pocketmine\level\generator\populator;

use pocketmine\block\Block;
use pocketmine\level\generator\populator\Populator;
use pocketmine\utils\Random;

abstract class VariableAmountPopulator extends Populator {

    protected $baseAmount;
    protected $randomAmount;
    protected $odd;

    public function __construct($baseAmount = 0, $randomAmount = 0, $odd = 0) {
        $this->baseAmount = $baseAmount;
        $this->randomAmount = $randomAmount;
        $this->odd = $odd;
    }

    public function setOdd($odd) {
        $this->odd = $odd;
    }

    public function checkOdd(Random $random) {
        if ($random->nextRange(0, $this->odd) == 0) {
            return true;
        }
        return false;
    }

    public function getAmount(Random $random) {
        return $this->baseAmount + $random->nextRange(0, $this->randomAmount + 1);
    }

    public final function setBaseAmount($baseAmount) {
        $this->baseAmount = $baseAmount;
    }

    public final function setRandomAmount($randomAmount) {
        $this->randomAmount = $randomAmount;
    }

    public function getBaseAmount() {
        return $this->baseAmount;
    }

    public function getRandomAmount() {
        return $this->randomAmount;
    }

    public function canStayAtBlock($x, $y, $z, $level) {
        $b = $level->getBlockIdAt($x, $y, $z);
        return ($b === Block::AIR or $b === Block::SNOW_LAYER) and $level->getBlockIdAt($x, $y - 1, $z) === Block::GRASS;
    }

    public function getHighestWorkableBlock($x, $z, $level) {
        for ($y = 0; $y <= 127; $y++) {
            $b = $level->getBlockIdAt($x, $y, $z);

            if (($b === Block::AIR or $b === Block::SNOW_LAYER) and $level->getBlockIdAt($x, $y - 1, $z) === Block::GRASS) {
                break;
            }
        }

        return $y === 0 ? -1 : $y;
    }
}