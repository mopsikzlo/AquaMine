<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\item\Tool;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;
use pocketmine\tile\Chest as TileChest;
use pocketmine\tile\Tile;

class Chest extends Transparent{

	protected $id = self::CHEST;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getHardness(){
		return 2.5;
	}

	public function getName(){
		return "Chest";
	}

	public function getToolType(){
		return Tool::TYPE_AXE;
	}

	protected function recalculateBoundingBox(){
		//these are slightly bigger than in PC
		return new AxisAlignedBB(
			$this->x + 0.025,
			$this->y,
			$this->z + 0.025,
			$this->x + 0.975,
			$this->y + 0.95,
			$this->z + 0.975
		);
	}

	public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null){
		static $faces = [
			0 => 4,
			1 => 2,
			2 => 5,
			3 => 3,
		];

		$chest = null;
		$this->meta = $faces[$player instanceof Player ? $player->getDirection() : 0];

		for($side = 2; $side <= 5; ++$side){
			if(($this->meta === 4 or $this->meta === 5) and ($side === 4 or $side === 5)){
				continue;
			}elseif(($this->meta === 3 or $this->meta === 2) and ($side === 2 or $side === 3)){
				continue;
			}
			$c = $this->getSide($side);
			if($c->getId() === $this->id and $c->getDamage() === $this->meta){
				$tile = $this->getLevel()->getTile($c);
				if($tile instanceof TileChest and !$tile->isPaired()){
					$chest = $tile;
					break;
				}
			}
		}

		$this->getLevel()->setBlock($block, $this, true, true);
		$nbt = CompoundTag::create()
			->setTag("Items", new ListTag([], NBT::TAG_Compound))
			->setString("id", Tile::CHEST)
			->setInt("x", $this->x)
			->setInt("y", $this->y)
			->setInt("z", $this->z);

		if($item->hasCustomName()){
			$nbt->setString("CustomName", $item->getCustomName());
		}

		if($item->hasCustomBlockData()){
			foreach($item->getCustomBlockData() as $k => $tag){
				$nbt->setTag($k, $tag);
			}
		}

		$tile = Tile::createTile("Chest", $this->getLevel(), $nbt);

		if($chest instanceof TileChest and $tile instanceof TileChest){
			$chest->pairWith($tile);
			$tile->pairWith($chest);
		}

		return true;
	}

	public function onBreak(Item $item){
		$t = $this->getLevel()->getTile($this);
		if($t instanceof TileChest){
			$t->unpair();
		}
		$this->getLevel()->setBlock($this, new Air(), true, true);

		return true;
	}

	public function onActivate(Item $item, Player $player = null){
		if($player instanceof Player){
			$top = $this->getSide(Vector3::SIDE_UP);
			if($top->isTransparent() !== true){
				return true;
			}

			$t = $this->getLevel()->getTile($this);
			$chest = null;
			if($t instanceof TileChest){
				$chest = $t;
			}else{
				$nbt = CompoundTag::create()
					->setTag("Items", new ListTag([], NBT::TAG_Compound))
					->setString("id", Tile::CHEST)
					->setInt("x", $this->x)
					->setInt("y", $this->y)
					->setInt("z", $this->z);
				$chest = Tile::createTile("Chest", $this->getLevel(), $nbt);
			}

			if($chest->namedtag->hasTag("Lock", StringTag::class)){
				if($chest->namedtag->getString("Lock") !== $item->getCustomName()){
					return true;
				}
			}

			$player->addWindow($chest->getInventory());
		}

		return true;
	}

	public function canBeActivated() : bool{
		return true;
	}

	public function getDrops(Item $item){
		return [
			[$this->id, 0, 1],
		];
	}
}