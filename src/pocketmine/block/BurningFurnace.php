<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\item\TieredTool;
use pocketmine\item\Tool;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;
use pocketmine\tile\Furnace as TileFurnace;
use pocketmine\tile\Tile;

class BurningFurnace extends Solid{

	protected $id = self::BURNING_FURNACE;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getName(){
		return "Burning Furnace";
	}

	public function getHardness(){
		return 3.5;
	}

	public function getToolType(){
		return Tool::TYPE_PICKAXE;
	}

	public function getToolHarvestLevel() : int{
		return TieredTool::TIER_WOODEN;
	}

	public function getLightLevel(){
		return 13;
	}

	public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null){
		$faces = [
			0 => 4,
			1 => 2,
			2 => 5,
			3 => 3,
		];
		$this->meta = $faces[$player instanceof Player ? $player->getDirection() : 0];
		$this->getLevel()->setBlock($block, $this, true, true);
		$nbt = CompoundTag::create()
			->setTag("Items", new ListTag([], NBT::TAG_Compound))
			->setString("id", Tile::FURNACE)
			->setInt("x", $this->x)
			->setInt("y", $this->y)
			->setInt("z", $this->z);

		if($item->hasCustomName()){
			$nbt->setString("CustomName", $item->getCustomName());
		}

		if($item->hasCustomBlockData()){
			foreach($item->getCustomBlockData() as $key => $v){
				$nbt->{$key} = $v;
			}
		}

		Tile::createTile("Furnace", $this->getLevel(), $nbt);

		return true;
	}

	public function onActivate(Item $item, Player $player = null){
		if($player instanceof Player){
			$furnace = $this->getLevel()->getTile($this);
			if(!($furnace instanceof TileFurnace)){
				$nbt = CompoundTag::create()
					->setTag("Items", new ListTag([], NBT::TAG_Compound))
					->setString("id", Tile::FURNACE)
					->setInt("x", $this->x)
					->setInt("y", $this->y)
					->setInt("z", $this->z);
				$furnace = Tile::createTile("Furnace", $this->getLevel(), $nbt);
			}

			if($furnace->namedtag->hasTag("Lock", StringTag::class)){
				if($furnace->namedtag->getString("Lock") !== $item->getCustomName()){
					return true;
				}
			}

			$player->addWindow($furnace->getInventory());
		}

		return true;
	}

	public function canBeActivated() : bool{
		return true;
	}

	public function getDrops(Item $item){
		$drops = [];
		if($item->isPickaxe() >= Tool::TIER_WOODEN){
			$drops[] = [Item::FURNACE, 0, 1];
		}

		return $drops;
	}
}