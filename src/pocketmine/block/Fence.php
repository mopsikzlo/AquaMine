<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\Tool;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;

class Fence extends Transparent{
	public const FENCE_OAK = 0;
	public const FENCE_SPRUCE = 1;
	public const FENCE_BIRCH = 2;
	public const FENCE_JUNGLE = 3;
	public const FENCE_ACACIA = 4;
	public const FENCE_DARKOAK = 5;

	protected $id = self::FENCE;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getHardness(){
		return 2;
	}

	public function getToolType(){
		return Tool::TYPE_AXE;
	}


	public function getName(){
		static $names = [
			self::FENCE_OAK => "Oak Fence",
			self::FENCE_SPRUCE => "Spruce Fence",
			self::FENCE_BIRCH => "Birch Fence",
			self::FENCE_JUNGLE => "Jungle Fence",
			self::FENCE_ACACIA => "Acacia Fence",
			self::FENCE_DARKOAK => "Dark Oak Fence",
			"",
			""
		];
		return $names[$this->meta & 0x07];
	}

	protected function recalculateBoundingBox(){

		$north = $this->canConnect($this->getSide(Vector3::SIDE_NORTH));
		$south = $this->canConnect($this->getSide(Vector3::SIDE_SOUTH));
		$west = $this->canConnect($this->getSide(Vector3::SIDE_WEST));
		$east = $this->canConnect($this->getSide(Vector3::SIDE_EAST));

		$n = $north ? 0 : 0.375;
		$s = $south ? 1 : 0.625;
		$w = $west ? 0 : 0.375;
		$e = $east ? 1 : 0.625;

		return new AxisAlignedBB(
			$this->x + $w,
			$this->y,
			$this->z + $n,
			$this->x + $e,
			$this->y + 1.5,
			$this->z + $s
		);
	}

	public function canConnect(Block $block){
		return $block instanceof Fence or $block instanceof FenceGate or ($block->isSolid() and !$block->isTransparent());
	}

}
