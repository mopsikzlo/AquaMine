<?php

declare(strict_types=1);

namespace pocketmine\level\generator\populator;

use pocketmine\block\Block;
use pocketmine\level\ChunkManager;
use pocketmine\level\generator\populator\VariableAmountPopulator;
use pocketmine\utils\Random;

class ForestGrass extends VariableAmountPopulator{
/** @var ChunkManager */
private $level;

public function populate(ChunkManager $level, $chunkX, $chunkZ, Random $random){
$this->level = $level;
$amount = $this->getAmount($random);
for($i = 0; $i < $amount; ++$i){
$x = $random->nextRange($chunkX * 16, $chunkX * 16 + 15);
$z = $random->nextRange($chunkZ * 16, $chunkZ * 16 + 15);
$y = $this->getHighestWorkableBlock($x, $z);

if($y !== -1 and $this->canForestGrassStay($x, $y, $z)){
 $rnd = mt_rand(1,5);
 if($rnd == 1){ 
$level->setBlockIdAt($x, $y, $z, 31);
$level->setBlockDataAt($x, $y, $z, 2);
 }elseif($rnd == 2){
$level->setBlockIdAt($x, $y, $z, 175);
$level->setBlockDataAt($x, $y, $z, 2);
$level->setBlockIdAt($x, $y+1, $z, 175);
$level->setBlockDataAt($x, $y+1, $z, 10);
}else{
$this->level->setBlockIdAt($x, $y, $z, 31);
$this->level->setBlockDataAt($x, $y, $z, 1);
 }
}
}
}

private function canForestGrassStay($x, $y, $z){
$b = $this->level->getBlockIdAt($x, $y, $z);
return ($b === Block::AIR or $b === Block::SNOW_LAYER) and ($this->level->getBlockIdAt($x, $y - 1, $z) === Block::GRASS or $this->level->getBlockIdAt($x, $y - 1, $z) === Block::DIRT);
}

private function getHighestWorkableBlock($x, $z){
for($y = 127; $y >= 0; --$y){
$b = $this->level->getBlockIdAt($x, $y, $z);
if($b !== Block::AIR and $b !== Block::LEAVES and $b !== Block::LEAVES2 and $b !== Block::SNOW_LAYER){
break;
}
}

return $y === 0 ? -1 :++$y;
}
}