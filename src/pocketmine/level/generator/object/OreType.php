<?php

namespace pocketmine\level\generator\object;

use pocketmine\block\Block;

class OreType {
	public $material, $clusterCount, $clusterSize, $maxHeight, $minHeight;

	/**
	 * OreType constructor.
	 *
	 * @param Block $material
	 * @param       $clusterCount
	 * @param       $clusterSize
	 * @param       $minHeight
	 * @param       $maxHeight
	 */
	public function __construct(Block $material, $clusterCount, $clusterSize, $minHeight, $maxHeight){
		$this->material = $material;
		$this->clusterCount = (int) $clusterCount;
		$this->clusterSize = (int) $clusterSize;
		$this->maxHeight = (int) $maxHeight;
		$this->minHeight = (int) $minHeight;
	}
}