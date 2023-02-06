<?php

declare(strict_types=1);

namespace pocketmine\event\player;

use pocketmine\entity\Skin;
use pocketmine\event\Cancellable;
use pocketmine\Player;

class PlayerSkinChangeEvent extends PlayerEvent implements Cancellable{
	public static $handlerList;

	/** @var Skin */
	protected $oldSkin;
	/** @var Skin */
	protected $newSkin;

	public function __construct(Player $player, Skin $oldSkin, Skin $newSkin){
		$this->player = $player;
		$this->oldSkin = $oldSkin;
		$this->newSkin = $newSkin;
	}

	/**
	 * @return Skin
	 */
	public function getOldSkin() : Skin{
		return $this->oldSkin;
	}

	/**
	 * @return Skin
	 */
	public function getNewSkin() : Skin{
		return $this->newSkin;
	}

	/**
	 * @param Skin $newSkin
	 */
	public function setNewSkin(Skin $newSkin) : void{
		$this->newSkin = $newSkin;
	}
}