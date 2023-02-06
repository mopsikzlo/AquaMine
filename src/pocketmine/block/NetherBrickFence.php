<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\item\TieredTool;
use pocketmine\item\Tool;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;

class NetherBrickFence extends Transparent{

	protected $id = self::NETHER_BRICK_FENCE;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getHardness(){
		return 2;
	}

	public function getToolType(){
		return Tool::TYPE_PICKAXE;
	}

	public function getName(){
		return "Nether Brick Fence";
	}

	public function getToolHarvestLevel() : int{
		return TieredTool::TIER_WOODEN;
	}

	protected function recalculateBoundingBox(){
		$width = 0.375;
		return new AxisAlignedBB(
			$this->x + ($this->canConnect($this->getSide(Vector3::SIDE_WEST)) ? 0 : $width),
			$this->y,
			$this->z + ($this->canConnect($this->getSide(Vector3::SIDE_NORTH)) ? 0 : $width),
			$this->x + 1 - ($this->canConnect($this->getSide(Vector3::SIDE_EAST)) ? 0 : $width),
			$this->y + 1.5,
			$this->z + 1 - ($this->canConnect($this->getSide(Vector3::SIDE_SOUTH)) ? 0 : $width)
		);
	}

	public function canConnect(Block $block){
		return $block instanceof NetherBrickFence or $block instanceof FenceGate or ($block->isSolid() and !$block->isTransparent());
	}

	public function getDrops(Item $item){
		if($item->isPickaxe() >= Tool::TIER_WOODEN){
			return [
				[$this->id, $this->meta, 1],
			];
		}else{
			return [];
		}
	}
}
