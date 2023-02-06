<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Player;
use pocketmine\tile\ItemFrame as TileItemFrame;
use pocketmine\tile\Tile;
use function lcg_value;

class ItemFrame extends Flowable{
	protected $id = Block::ITEM_FRAME_BLOCK;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getName(){
		return "Item Frame";
	}

	public function onActivate(Item $item, Player $player = null){
		$tile = $this->level->getTileAt($this->x, $this->y, $this->z);
		if(!($tile instanceof TileItemFrame)){
			$nbt = CompoundTag::create()
				->setString("id", Tile::ITEM_FRAME)
				->setInt("x", $this->x)
				->setInt("y", $this->y)
				->setInt("z", $this->z)
				->setFloat("ItemDropChance", 1.0)
				->setByte("ItemRotation", 0);
			$tile = Tile::createTile(Tile::ITEM_FRAME, $this->getLevel(), $nbt);
		}

		if($tile->hasItem()){
			$tile->setItemRotation(($tile->getItemRotation() + 1) % 8);
		}else{
			if($item->getCount() > 0){
				$frameItem = clone $item;
				$frameItem->setCount(1);
				$item->setCount($item->getCount() - 1);
				$tile->setItem($frameItem);
				if($player instanceof Player and $player->isSurvival()){
					$player->getInventory()->setItemInHand($item->getCount() <= 0 ? Item::get(Item::AIR) : $item);
				}
			}
		}

		return true;
	}

	public function canBeActivated() : bool{
		return true;
	}

	public function onBreak(Item $item){
		$tile = $this->level->getTile($this);
		if($tile instanceof TileItemFrame){
			//TODO: add events
			if(lcg_value() <= $tile->getItemDropChance() and $tile->getItem()->getId() !== Item::AIR){
				$this->level->dropItem($tile->getBlock(), $tile->getItem());
			}
		}
		return parent::onBreak($item);
	}

	public function onUpdate($type){
		if($type === Level::BLOCK_UPDATE_NORMAL){
			$sides = [
				0 => 4,
				1 => 5,
				2 => 2,
				3 => 3
			];
			if(!$this->getSide($sides[$this->meta])->isSolid()){
				$this->level->useBreakOn($this);
				return Level::BLOCK_UPDATE_NORMAL;
			}
		}
		return false;
	}

	public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null){
		if($face === 0 or $face === 1){
			return false;
		}

		$faces = [
			2 => 3,
			3 => 2,
			4 => 1,
			5 => 0
		];

		$this->meta = $faces[$face];
		$this->level->setBlock($block, $this, true, true);

		$nbt = CompoundTag::create()
			->setString("id", Tile::ITEM_FRAME)
			->setInt("x", $this->x)
			->setInt("y", $this->y)
			->setInt("z", $this->z)
			->setFloat("ItemDropChance", 1.0)
			->setByte("ItemRotation", 0);

		if($item->hasCustomBlockData()){
			foreach($item->getCustomBlockData() as $key => $v){
				$nbt->setTag($key, $v);
			}
		}

		Tile::createTile(Tile::ITEM_FRAME, $this->getLevel(), $nbt);

		return true;

	}

	public function getDrops(Item $item){
		return [
			[Item::ITEM_FRAME, 0, 1]
		];
	}

}