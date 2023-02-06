<?php

declare(strict_types=1);

namespace pocketmine\command;

use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\plugin\Plugin;

class PluginCommand extends Command implements PluginIdentifiableCommand{

	/** @var Plugin */
	private $owningPlugin;

	/** @var CommandExecutor */
	private $executor;

	/**
	 * @param string $name
	 * @param Plugin $owner
	 */
	public function __construct($name, Plugin $owner){
		parent::__construct($name);
		$this->owningPlugin = $owner;
		$this->executor = $owner;
		$this->usageMessage = "";
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){

		if(!$this->owningPlugin->isEnabled()){
			return false;
		}

		if(!$this->testPermission($sender)){
			return false;
		}

		$success = $this->executor->onCommand($sender, $this, $commandLabel, $args);

		if(!$success and $this->usageMessage !== ""){
			throw new InvalidCommandSyntaxException();
		}

		return $success;
	}

	public function getExecutor() : CommandExecutor{
		return $this->executor;
	}

	/**
	 * @param CommandExecutor $executor
	 */
	public function setExecutor(CommandExecutor $executor){
		$this->executor = $executor;
	}

	/**
	 * @return Plugin
	 */
	public function getPlugin() : Plugin{
		return $this->owningPlugin;
	}
}
