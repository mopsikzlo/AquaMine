<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\item\Shovel;
use pocketmine\item\TieredTool;
use pocketmine\item\Tool;
use pocketmine\level\Level;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use pocketmine\Player;

class SnowLayer extends Flowable{

	protected $id = self::SNOW_LAYER;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getName(){
		return "Snow Layer";
	}

	public function canBeReplaced(){
		return true;
	}

	public function getHardness(){
		return 0.1;
	}

	public function getToolType(){
		return Tool::TYPE_SHOVEL;
	}

	public function getToolHarvestLevel() : int{
		return TieredTool::TIER_WOODEN;
	}

	protected function recalculateBoundingBox(){
		$bb = AxisAlignedBB::one();
		if($this->meta < 7 and $this->meta >= 3){
			$bb->maxY -= 0.5;
		}else{
			$bb->maxY -= 1.0;
		}
		return $bb;
	}

	public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null){
		if($block instanceof SnowLayer){
			if($block->getDamage() >= 7){
				return false;
			}
			$this->meta = $block->getDamage() + 1;
		}

		if($block->getSide(Vector3::SIDE_DOWN)->isSolid()){
			//TODO: fix placement
			$this->getLevel()->setBlock($block, $this, true);

			return true;
		}

		return false;
	}

	public function onUpdate($type){
		if($type === Level::BLOCK_UPDATE_NORMAL){
			if(!$this->getSide(Vector3::SIDE_DOWN)->isSolid()){
				$this->getLevel()->setBlock($this, Block::get(Block::AIR), false, false);

				return Level::BLOCK_UPDATE_NORMAL;
			}
		}elseif($type === Level::BLOCK_UPDATE_RANDOM){
			if($this->level->getBlockLightAt($this->x, $this->y, $this->z) >= 12){
				$this->getLevel()->setBlock($this, Block::get(Block::AIR), false, false);

				return Level::BLOCK_UPDATE_RANDOM;
			}
		}

		return false;
	}

	public function getDrops(Item $item){
		if($item instanceof Shovel !== false){
			return [
				[Item::SNOWBALL, 0, max(1, (int) floor(($this->getDamage() + 1) / 2))],
			];
		}

		return [];
	}

	//TODO: bounding & collision boxes
}