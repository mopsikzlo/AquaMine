<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\BedrockPlayer;
use pocketmine\item\Item;
use pocketmine\item\Tool;
use pocketmine\network\bedrock\protocol\ContainerOpenPacket;
use pocketmine\network\bedrock\protocol\types\inventory\WindowTypes;
use pocketmine\network\mcpe\protocol\types\ContainerIds;
use pocketmine\Player;

class CraftingTable extends Solid{

	protected $id = self::CRAFTING_TABLE;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getHardness(){
		return 2.5;
	}

	public function getName(){
		return "Crafting Table";
	}

	public function onActivate(Item $item, Player $player = null){
		if($player instanceof Player){
			$player->craftingType = Player::CRAFTING_BIG;

			if($player instanceof BedrockPlayer and $player->newInventoryOpen(ContainerIds::INVENTORY)){
				$pk = new ContainerOpenPacket();
				$pk->windowId = ContainerIds::INVENTORY;
				$pk->type = WindowTypes::WORKBENCH;
				[$pk->x, $pk->y, $pk->z] = [$this->x, $this->y, $this->z];
				$player->sendDataPacket($pk);
			}
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
