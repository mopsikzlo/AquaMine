<?php

declare(strict_types=1);

namespace pocketmine\command;

use pocketmine\event\TextContainer;
use pocketmine\permission\PermissibleBase;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionAttachment;
use pocketmine\permission\PermissionAttachmentInfo;
use pocketmine\plugin\Plugin;
use pocketmine\Server;
use pocketmine\utils\MainLogger;

use function explode;
use function trim;

use const PHP_INT_MAX;

class ConsoleCommandSender implements CommandSender{

	private $perm;

	/** @var int|null */
	protected $lineHeight = null;

	public function __construct(){
		$this->perm = new PermissibleBase($this);
	}

	/**
	 * @param Permission|string $name
	 *
	 * @return bool
	 */
	public function isPermissionSet($name) : bool{
		return $this->perm->isPermissionSet($name);
	}

	/**
	 * @param Permission|string $name
	 *
	 * @return bool
	 */
	public function hasPermission($name) : bool{
		return $this->perm->hasPermission($name);
	}

	/**
	 * @param Plugin $plugin
	 * @param string $name
	 * @param bool $value
	 *
	 * @return PermissionAttachment
	 */
	public function addAttachment(Plugin $plugin, string $name = null, bool $value = null) : PermissionAttachment{
		return $this->perm->addAttachment($plugin, $name, $value);
	}

	/**
	 * @param PermissionAttachment $attachment
	 *
	 * @return void
	 */
	public function removeAttachment(PermissionAttachment $attachment){
		$this->perm->removeAttachment($attachment);
	}

	public function recalculatePermissions(){
		$this->perm->recalculatePermissions();
	}

	/**
	 * @return PermissionAttachmentInfo[]
	 */
	public function getEffectivePermissions() : array{
		return $this->perm->getEffectivePermissions();
	}

	/**
	 * @return bool
	 */
	public function isPlayer() : bool{
		return false;
	}

	/**
	 * @return Server
	 */
	public function getServer(){
		return Server::getInstance();
	}

	/**
	 * @param TextContainer|string $message
	 */
	public function sendMessage($message){
		if($message instanceof TextContainer){
			$message = $this->getServer()->getLanguage()->translate($message);
		}else{
			$message = $this->getServer()->getLanguage()->translateString($message);
		}

		foreach(explode("\n", trim($message)) as $line){
			MainLogger::getLogger()->info($line);
		}
	}

	/**
	 * @param TextContainer|string $message
	 */
	public function sendCommandMessage($message){
		$this->sendMessage($message);
	}

	/**
	 * @return string
	 */
	public function getName(){
		return "CONSOLE";
	}

	/**
	 * @return bool
	 */
	public function isOp() : bool{
		return true;
	}

	/**
	 * @param bool $value
	 */
	public function setOp(bool $value){

	}

	public function getScreenLineHeight() : int{
		return $this->lineHeight ?? PHP_INT_MAX;
	}

	public function setScreenLineHeight(int $height = null){
		if($height !== null and $height < 1){
			throw new \InvalidArgumentException("Line height must be at least 1");
		}
		$this->lineHeight = $height;
	}

}