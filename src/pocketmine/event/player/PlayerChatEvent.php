<?php

declare(strict_types=1);

namespace pocketmine\event\player;

use pocketmine\event\Cancellable;
use pocketmine\Player;
use pocketmine\Server;

/**
 * Called when a player chats something
 */
class PlayerChatEvent extends PlayerEvent implements Cancellable{
	public static $handlerList = null;

	/** @var string */
	protected $message;

	/** @var string */
	protected $format;

	/**
	 * @var Player[]
	 */
	protected $recipients = [];

	/**
	 * @param Player   $player
	 * @param string   $message
	 * @param string   $format
	 * @param Player[] $recipients
	 */
	public function __construct(Player $player, string $message, string $format = "chat.type.text", array $recipients = null){
		$this->player = $player;
		$this->message = $message;

		$this->format = $format;

		if($recipients === null){
			$this->recipients = Server::getInstance()->getPluginManager()->getPermissionSubscriptions(Server::BROADCAST_CHANNEL_USERS);
		}else{
			$this->recipients = $recipients;
		}
	}

	/**
	 * @return string
	 */
	public function getMessage() : string{
		return $this->message;
	}

	/**
	 * @param string $message
	 */
	public function setMessage(string $message){
		$this->message = $message;
	}

	/**
	 * Changes the player that is sending the message
	 *
	 * @param Player $player
	 */
	public function setPlayer(Player $player){
		$this->player = $player;
	}

	/**
	 * @return string
	 */
	public function getFormat() : string{
		return $this->format;
	}

	/**
	 * @param string $format
	 */
	public function setFormat(string $format){
		$this->format = $format;
	}

	/**
	 * @return Player[]
	 */
	public function getRecipients() : array{
		return $this->recipients;
	}

	/**
	 * @param Player[] $recipients
	 */
	public function setRecipients(array $recipients){
		$this->recipients = $recipients;
	}
}