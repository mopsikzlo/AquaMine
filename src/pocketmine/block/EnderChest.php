<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\item\TieredTool;
use pocketmine\item\Tool;
use pocketmine\math\Vector3;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\Player;
use pocketmine\tile\EnderChest as TileEnderChest;
use pocketmine\tile\Tile;

class EnderChest extends Chest{

	protected $id = self::ENDER_CHEST;

	public function getHardness() : float{
		return 22.5;
	}

	public function getBlastResistance() : float{
		return 3000;
	}

	public function getLightLevel() : int{
		return 7;
	}

	public function getName() : string{
		return "Ender Chest";
	}

	public function getToolType() : int{
		return Tool::TYPE_PICKAXE;
	}

	public function getToolHarvestLevel() : int{
		return TieredTool::TIER_WOODEN;
	}

	public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null){
		static $faces = [
			0 => 4,
			1 => 2,
			2 => 5,
			3 => 3
		];

		$this->meta = $faces[$player instanceof Player ? $player->getDirection() : 0];

		$this->getLevel()->setBlock($block, $this, true, true);
		$nbt = CompoundTag::create()
			->setTag("Items", new ListTag([], NBT::TAG_Compound))
			->setString("id", Tile::ENDER_CHEST)
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
		Tile::createTile(Tile::ENDER_CHEST, $this->getLevel(), $nbt);

		return true;
	}

	public function onBreak(Item $item) : bool{
		return Block::onBreak($item);
	}

	public function onActivate(Item $item, Player $player = null){
		if($player instanceof Player){
			$t = $this->getLevel()->getTileAt($this->x, $this->y, $this->z);
			$enderChest = null;
			if($t instanceof TileEnderChest){
				$enderChest = $t;
			}else{
				$nbt = CompoundTag::create()
					->setTag("Items", new ListTag([], NBT::TAG_Compound))
					->setString("id", Tile::ENDER_CHEST)
					->setInt("x", $this->x)
					->setInt("y", $this->y)
					->setInt("z", $this->z);

				$enderChest = Tile::createTile(Tile::ENDER_CHEST, $this->getLevel(), $nbt);
			}

			if(!$this->getSide(Vector3::SIDE_UP)->isTransparent()){
				return true;
			}

			$player->getEnderChestInventory()->setHolderPosition($enderChest);
			$player->addWindow($player->getEnderChestInventory());
		}

		return true;
	}

	public function getDrops(Item $item) : array{
		if($item->isPickaxe() >= Tool::TIER_WOODEN){
			return [
				[Item::OBSIDIAN, 0, 8]
			];
		}

		return [];
	}

}