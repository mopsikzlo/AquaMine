<?php

declare(strict_types=1);

namespace pocketmine\inventory;

use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\ContainerClosePacket;
use pocketmine\network\mcpe\protocol\ContainerOpenPacket;
use pocketmine\network\bedrock\protocol\ContainerClosePacket as BedrockContainerClose;
use pocketmine\Player;
use pocketmine\BedrockPlayer;

abstract class ContainerInventory extends BaseInventory{

	public function onOpen(Player $who){
		parent::onOpen($who);

		$pk = new ContainerOpenPacket();
		$pk->windowId = $who->getWindowId($this);
		$pk->type = $this->getType()->getNetworkType();
		$holder = $this->getHolder();
		if($holder instanceof Vector3){
			$pk->x = $holder->getX();
			$pk->y = $holder->getY();
			$pk->z = $holder->getZ();
		}else{
			$pk->x = $pk->y = $pk->z = 0;
		}

		$who->sendDataPacket($pk);

		$this->sendContents($who);
	}

	public function onClose(Player $who){
		if($who instanceof BedrockPlayer){
			$pk = new BedrockContainerClose();
			$pk->windowId = $who->getWindowId($this);
			$pk->server = $who->getClientClosingWindowId() !== $pk->windowId;
			$who->sendDataPacket($pk);
		}else{
			$pk = new ContainerClosePacket();
			$pk->windowId = $who->getWindowId($this);
			$who->sendDataPacket($pk);
		}

		parent::onClose($who);
	}
}