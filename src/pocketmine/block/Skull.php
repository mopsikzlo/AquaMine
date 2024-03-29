<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\math\AxisAlignedBB;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;
use pocketmine\tile\Skull as SkullTile;
use pocketmine\tile\Spawnable;
use pocketmine\tile\Tile;

use function floor;

class Skull extends Flowable{

	protected $id = self::SKULL_BLOCK;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getHardness(){
		return 1;
	}

	public function getName(){
		return "Mob Head Block";
	}

	protected function recalculateBoundingBox(){
		//TODO: different bounds depending on attached face (meta)
		return new AxisAlignedBB(
			$this->x + 0.25,
			$this->y,
			$this->z + 0.25,
			$this->x + 0.75,
			$this->y + 0.5,
			$this->z + 0.75
		);
	}

	public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null){
		if($face !== 0){
			$this->meta = $face;
			if($face === 1){
				$rot = floor(($player->yaw * 16 / 360) + 0.5) & 0x0F;
			}else{
				$rot = $face;
			}
			$this->getLevel()->setBlock($block, $this, true);
			$nbt = CompoundTag::create()
				->setString("id", Tile::SKULL)
				->setByte("SkullType", $item->getDamage())
				->setByte("Rot", $rot)
				->setInt("x", (int) $this->x)
				->setInt("y", (int) $this->y)
				->setInt("z", (int) $this->z);

			if($item->hasCustomName()){
				$nbt->setString("CustomName", $item->getCustomName());
			}
			/** @var Spawnable $tile */
			Tile::createTile("Skull", $this->getLevel(), $nbt);
			return true;
		}
		return false;
	}

	public function getDrops(Item $item){
		$tile = $this->level->getTileAt($this->x, $this->y, $this->z);
		if($tile instanceof SkullTile){
			return [
				[Item::SKULL, $tile->getType(), 1]
			];
		}

		return [];
	}

	public function getPickedItem() : Item{
		$tile = $this->level->getTileAt($this->x, $this->y, $this->z);
		if($tile instanceof SkullTile){
			return Item::get(Item::SKULL, $tile->getType(), 1);
		}

		return Item::get(Item::SKULL, 0, 1);
	}
}