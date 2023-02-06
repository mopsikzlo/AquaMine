<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\inventory\AnvilInventory;
use pocketmine\item\Item;
use pocketmine\item\TieredTool;
use pocketmine\item\Tool;
use pocketmine\Player;

class Anvil extends Fallable{

	public const TYPE_NORMAL = 0;
	public const TYPE_SLIGHTLY_DAMAGED = 4;
	public const TYPE_VERY_DAMAGED = 8;

	protected $id = self::ANVIL;

	public function isSolid(){
		return false;
	}

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getHardness(){
		return 5;
	}

	public function getBlastResistance() : float{
		return 6000;
	}

	public function getName(){
		static $names = [
			self::TYPE_NORMAL => "Anvil",
			self::TYPE_SLIGHTLY_DAMAGED => "Slightly Damaged Anvil",
			self::TYPE_VERY_DAMAGED => "Very Damaged Anvil"
		];
		return $names[$this->meta & 0x0c] ?? "Anvil";
	}

	public function getToolType(){
		return Tool::TYPE_PICKAXE;
	}

	public function getToolHarvestLevel() : int{
		return TieredTool::TIER_WOODEN;
	}

	public function onActivate(Item $item, Player $player = null){
		if($player instanceof Player){
			$player->addWindow(new AnvilInventory($this));
			$player->craftingType = Player::CRAFTING_ANVIL;
		}

		return true;
	}

	public function canBeActivated() : bool{
		return true;
	}

	public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null){
		$direction = ($player !== null ? $player->getDirection() : 0) & 0x03;
		$this->meta = ($this->meta & 0x0c) | $direction;
		$this->getLevel()->setBlock($block, $this, true, true);
	}

	public function getDrops(Item $item){
		if($item->isPickaxe() >= Tool::TIER_WOODEN){
			return [
				[$this->id, $this->meta & 0x0c, 1],
			];
		}else{
			return [];
		}
	}
}