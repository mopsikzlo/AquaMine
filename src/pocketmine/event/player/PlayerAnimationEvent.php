<?php

declare(strict_types=1);

namespace pocketmine\event\player;

use pocketmine\event\Cancellable;
use pocketmine\Player;

/**
 * Called when a player does an animation
 */
class PlayerAnimationEvent extends PlayerEvent implements Cancellable{
	public static $handlerList = null;

	/**
	 * @deprecated This is dependent on the protocol and should not be here.
	 * Use the constants in {@link pocketmine\network\mcpe\protocol\AnimatePacket} instead.
	 */
	public const ARM_SWING = 1;

	/** @var int */
	private $animationType;

	/**
	 * @param Player $player
	 * @param int    $animation
	 */
	public function __construct(Player $player, $animation = self::ARM_SWING){
		$this->player = $player;
		$this->animationType = $animation;
	}

	/**
	 * @return int
	 */
	public function getAnimationType() : int{
		return $this->animationType;
	}

}